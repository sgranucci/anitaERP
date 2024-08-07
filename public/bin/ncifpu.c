/*
 *
 * Procesa un archivo documento de Word (por ej.) grabado al disco y
 * procede a reemplazar los campos definidos en un archivo
 *
 */


#include <stdlib.h>
#include <string.h>
#include <stdio.h>
#include <fcntl.h>
#include <unistd.h>
#include <time.h>


/*
#define DEBUG         		1
*/


#define NCIFPU_VER			"ncifpu 2.0 Rel. 21/01/2000"
#define NCIFPU_CTRL			"#ifpu2"

#define NKEYS 			4096
#define BUFFER			2048

#define FALSE			0
#define TRUE			1

#define OPEN_PRN		1
#define READ_PRN		2
#define EXEC_PRN		3
#define MAX_NKEYS		4

#define LOG_LOW			1
#define LOG_MEDIUM		2
#define LOG_HIGH		3

#define MASK			'@'

#define PRINTFORM_VAR	15
#define REPLACE_VAR		41
#define END_OF_VAR		199


#define INI				"/etc/ncifpu.ini"
#define LOG_FILE	    "/tmp/ncifpu.log"



/* Variables Locales */
int         LogLevel = LOG_LOW; 

static char FormPath[BUFFER],
			Form[BUFFER],
			LogFile[BUFFER];

static int  FormCopies, nkeys, FormTrim;
static char PadCh;

static FILE  *fh_in, *fh_log;

static struct key {
	char *search;
	char *replace;
	char  justify;
	char  padch;
    } keydata[NKEYS];



main(argc, argv) 
int    argc;
char **argv;
{                
	FILE        *f_in;
	char         line[BUFFER+1];
	register int i;
	int          err, opcion, flag, fchmod, ProcessLine(), ProcessFile();
	void         ReadIni(), OpenFile(), PrintFile();
	time_t		 ltime;


	flag = nkeys = 0;
	f_in  = stdin;

	FormCopies = 1;
	PadCh      = ' ';
	FormTrim   = FALSE;

	if ( argc == 1 )
	{
		/* Leo Configuracion */
		ReadIni();

		/* Abro archivo LOG */
		if ( strlen( LogFile ) > 0 )
		{
			fchmod = ( access( LogFile, F_OK ) == -1 ) ? TRUE : FALSE;

			if ( ( fh_log = fopen( LogFile, "a" )) == NULL )
			{
				printf( "Error abriendo archivo: %s\n", LogFile );
				fclose ( fh_log );
		
				exit( 0 );
			}

			if ( fchmod )
				chmod( LogFile, 0666 );
		}

		time( &ltime );
		fprintf( fh_log, "Version: %s\n", NCIFPU_VER );
		fprintf( fh_log, "Proceso iniciado el %s", ctime( &ltime ) );
		fprintf( fh_log, "Iniciado por %s - Log: %d\n", 
						  getlogin(), LogLevel );


		/* Procedo a leer stdin hasta que encuentre un EOF */
		while( !feof( f_in ) )
		{
	    	if ( fgets( line, 254, f_in ) == (char *)NULL )
	    	{
            	fclose( f_in );
        		fclose( fh_log );

				/* printf( "Error en lectura de Datos.\n" ); */
            	exit( 0 );
	    	}
			else
			{
				if ( strncmp( line, NCIFPU_CTRL, 6 ) != 0 && flag == FALSE )
				{
					if ( LogLevel >= LOG_LOW )
					{
						fprintf( fh_log, "La TABLA a procesar no es un formato ifpu compatible!!\n" );
						fprintf( fh_log, "Proceso abortado.\n" );
					}

            		fclose( f_in );
        			fclose( fh_log );

            		exit( 0 );
				}
				
				flag = TRUE;

				if ( *line != '#' &&  *line != ' ' )
				{
					err = ProcessLine( line );

					if ( err == MAX_NKEYS )
						break;
					else
						if ( err == EXEC_PRN )
						{
							/* Imprime el Archivo */
							PrintFile();

							/* Inicializa el Vector */
							for( i = 0; i < nkeys; i++ )
				      	  		keydata[i].search = keydata[i].replace = '\0';
								keydata[nkeys].justify = LEFT_ALIGN;
								keydata[nkeys].padch   = 32;	

							nkeys = 0;
						}
				}
			}
		}

		/* Imprime el Archivo */
		PrintFile();

		time( &ltime );
		fprintf( fh_log, "Proceso finalizado el %s", ctime( &ltime ) );
        fprintf( fh_log, "===============================================================================\n" );

		/* Cierre de Archivos */
        fclose( f_in );
        fclose( fh_in );
        fclose( fh_log );
	}
	else
		printf( "Usar: %s <archivo>\n", argv[0] );
		
}


/* 
 * Procesa el Archivo una vez por cada printform
 */
void PrintFile( void )
{
	register int i;


	/* Graba informacion en el LOG de la tabla a procesar */
	if ( LogLevel == LOG_HIGH )
	{
		fprintf( fh_log, "Nro. de Copias: %d\n", FormCopies );
		fprintf( fh_log, "Datos de la TABLA a procesar:\n" );
		for( i = 0; i < nkeys; i++ )
			fprintf( fh_log, "ID: %d - [%s] < [%s] : [%c] [%c]\n", 
				      	  	  i, keydata[i].search, keydata[i].replace,
				      	  	     keydata[i].justify, keydata[i].padch );
	}
	
	/* Procesa el Archivo */
	for( i = 0; i < FormCopies; i++)
   		ProcessFile();
}


/* 
 * Procesa una linea para determinar si es un SET o TAG
 */
int  ProcessLine( char *strin )
{
	char *token, *pdest, strtmp[BUFFER], seps[] = " ", 
	     *StrSave(), *StrInsChr();
	int   err, parser = 0, result;


	err = 0;

	/* Si encuentro en el String {} lo reemplazo por { } */
	pdest = strstr( strin, "{}" );

	if ( pdest != NULL )
	{
		result = pdest - strin + 1;
		StrInsChr( strin, ' ', result );
	}

	strcpy( strtmp, strin );

	token = strtok( strin, seps );
	while( token != NULL )
	{
		switch( parser )
		{
			/* INSTRUCCIONES */
			case 0 :
				if ( strcmp( token, "printform" ) == 0 )
				{
					parser = PRINTFORM_VAR;
					err = EXEC_PRN;
				}
				
				if ( strcmp( token, "set" ) == 0 )
					parser = 10;
				else
					if ( strcmp( token, "tag" ) == 0 )
						parser = 20;
				
				break;

			/* COMANDOS */
			case 10 :
				/* Acepta: set formpath directorio */
				if ( strcmp( token, "formpath" ) == 0 )
					parser = 200;
				else
					/* Acepta: set form archivo */
					if ( strcmp( token, "form" ) == 0 )
						parser = 201;
					else
						/* Acepta: set trim on/off */
						if ( strcmp( token, "trim" ) == 0 )
							parser = 202;

				break;

			case 15 :
				FormCopies = atoi( token );	
				break;

			/* VARIABLES INSTRUCCION SET */
			case 20 :
				if ( strcmp( token, "-justify" ) == 0 )
					parser = 30;
				else
					if ( strcmp( token, "-padch" ) == 0 )
						parser = 31;
					else
					{
						if ( keydata[nkeys].justify == '\0' )
							keydata[nkeys].justify = LEFT_ALIGN;
							
						if ( keydata[nkeys].padch == '\0' )
							keydata[nkeys].padch = 32;	

						keydata[nkeys].search  = StrSave( token );
						parser = REPLACE_VAR;
					}	
				break;

			case 30 :
				if ( strcmp( token, "left" ) == 0 )
					keydata[nkeys].justify = LEFT_ALIGN;
				else
					if ( strcmp( token, "right" ) == 0 )
						keydata[nkeys].justify = RIGHT_ALIGN;
					else
						if ( strcmp( token, "center" ) == 0 )
							keydata[nkeys].justify = CENTER_ALIGN;
						else
							/* Default */
							keydata[nkeys].justify = '\0';

				parser = 20;
				break;

			case 31 :
				keydata[nkeys].padch = atoi( token );	
				parser = 20;

				break;

			case 41 :
				keydata[nkeys].replace = StrSave( token );

				parser = END_OF_VAR;
				err    = READ_PRN;

                nkeys++;
				if ( nkeys >= NKEYS )
					err = MAX_NKEYS;

				break;

			/* VARIABLES INSTRUCCION TAG */
			case 200 :
				strcpy( FormPath, token );
				parser = 0;

				break;

			case 201 :
				strcpy( Form, token );
				err    = OPEN_PRN;
				parser = 0;

			case 202 :
				if (strcmp( token, "on" ) == 0)
					FormTrim = TRUE;
				else
					FormTrim = FALSE;

				parser = 0;

				break;
		}

		/* Obtine el proximo token */
		if ( parser == END_OF_VAR )
		{
			token = strtok( strtmp, seps );
			strcpy( seps, " " );
			parser = 0;
		}
		else
			if ( parser == PRINTFORM_VAR || parser == REPLACE_VAR )
			{
				token = strtok( strtmp, "{}" );
				strcpy( seps, "{}" );
			}

		token = strtok( NULL, seps );
	}
	return( err );
}


/* 
 * Abre archivo a procesar y de control 
 */
void OpenFile( void )
{
	char file[BUFFER];


	sprintf( file, "%s/%s", FormPath, Form );

	/* Verifica si existe el Archivo a procesar */
	if ( (access( file, 0 )) != 0 )
	{
		fprintf( fh_log, "No existe el archivo: %s\n", file );
		fclose ( fh_log );

		exit( 0 );
	}

	/* Abro archivo PRN */
	if ( ( fh_in = fopen( file, "r" )) == NULL )
	{
		fprintf( fh_log, "Error abriendo archivo: %s\n", file );
		fclose ( fh_log );

		exit( 0 );
	}

	fprintf( fh_log, "Archivo a procesar: [%s]\n", file );
	fprintf( fh_log, "Ajuste de Linea   : [%d]\n", FormTrim );
}


/* 
 * Procesa el archivo en busca de las claves a reemplazar 
 */
int  ProcessFile( void )
{
    char          Buffer[BUFFER], Replace[BUFFER];
	register int  byte, nmask, nbytes, flag;
	int           SearchKey();
	void          OpenFile(), SaveString(), SaveByte();


	if ( LogLevel < LOG_HIGH )
		fprintf( fh_log, "Iniciando la Busqueda en el archivo ...\n" );

	flag = nmask = nbytes = 0;

    /* Abre archivo a procesar y archivo de control */
	OpenFile();
	
	while ( !feof( fh_in ) )
	{
		/* Leo Byte */
		if (( byte = getc( fh_in )) == EOF )
			return;

		/* Comparo que no exceda el Max. de BUFFER */
		if ( nbytes >= BUFFER )
		{
			Buffer[nbytes] = '\0';

			SaveString( Buffer, nbytes );
			flag = nbytes = nmask = 0;
		}
		
		/* Comparo el byte leido con la Mascara */
		if ( byte == MASK )
		{
			if ( flag != 0 )
			{
				Buffer[nbytes] = '\0';

				SaveString( Buffer, nbytes );
				nbytes = nmask = 0;
			}

			nmask++;
			flag = 1;
			Buffer[nbytes++] = byte;

			/*
			 * Si encontro 2 mascaras, entonces encontro un clave 
			 * a reemplazar
			 */
			if ( nmask == 2 )
			{
				Buffer[nbytes] = '\0';

				/* Busco Buffer y si lo encuentra lo reemplaza por Replace */
				if ( SearchKey( Buffer, Replace ) )
					SaveString( Replace, nbytes );
				else
					SaveString( Buffer, nbytes );

				flag = nbytes = nmask = 0;
            }
		}
		else
		{
			if ( nmask > 0 )
				if ( isprint( byte ) )
				{
					flag = 0;
					Buffer[nbytes++] = byte;
				}
				else
				{
					Buffer[nbytes] = '\0';

				    SaveString( Buffer, nbytes );
					SaveByte( byte );

				    flag = nbytes = nmask = 0;
				}
			else
				SaveByte( byte );
		}
	}
	return;
}



void ReadIni( void )
{
	int  value;
    

    /* Directorios */
	value = GetPrivateProfileString( "DIR", "LogFile", LOG_FILE, LogFile, 80, INI  );

    /* Leo nivel de DEBUG */
	LogLevel = GetPrivateProfileInt( "DIR", "LogLevel", LOG_LOW, INI  );
}




/* 
 * Busca un string en la tabla y reemplaza strout por el valor de replace
 * Retorna 1 si encontro el string
 *         0 en caso contrario
 */ 
int SearchKey( char *strin, char *strout )
{
	register int i;
 	char *checkParentheses();


	if ( LogLevel >= LOG_MEDIUM )
		fprintf( fh_log, "-- Buscando: [%s] ...\n", strin );

	for( i = 0; i < nkeys; i++ )
		if ( strcmp( keydata[i].search, strin ) == 0 )
		{
			if ( keydata[i].justify != '\0' )
				StrAlign( strout, keydata[i].replace, strlen(strin), keydata[i].justify );
			else
				strcpy( strout, keydata[i].replace );

			PadCh = keydata[i].padch;


            /* Controla que no exista parentesis que no balanceen */
			strout[strlen(strin)] = '\0';
			checkParentheses( strout );
printf("encontro [%s]\n", strout);
			if ( LogLevel >= LOG_MEDIUM )
			{
				fprintf( fh_log, "   Encontrado en ID: %d\n", i );
				fprintf( fh_log, "-- Reemplazando por: [%s]\n", strout );
			}

			return( 1 );
        }

	return( 0 );
}


/*
 * Graba un BYTE en el dispositivo de salida 
 */
void SaveByte( int inbyte )
{
	putc( inbyte, stdout );

	if ( LogLevel == LOG_HIGH )
		fprintf( fh_log, "> Byte grabado: [%c] [%2.2x]\n", inbyte, inbyte );
}

/*
 * Copia el Contenido de un string en el dispositivo de salida 
 */
void SaveString( char *strin, int nbytes )
{
	register int   i, len ;
	register char *pstrin;

	char *strtmp;


	len = strlen( strin );

	/* Si FormTrim esta habilitado igualo los largos */
	if ( FormTrim )
		nbytes = len;

	if ( LogLevel == LOG_HIGH )
	{
		fprintf( fh_log, "Grabando [%s] - largo real %d, requerido %d\n",
						  strin, len, nbytes );
	}

	for( i = 0, pstrin = strin; i < nbytes; i++ )
		/* Si el string a reemplazar en < completar com espacios */
		if ( i >= len )
			putc( PadCh, stdout );  
		else
			putc( *pstrin++, stdout );  

	PadCh = ' ';
}

/*
 * Controla que los parentesis se encuentren balanceados 
 */
char *checkParentheses( char *strin )
{
	register int i, j;
	int count = 0;

	
    if (*strin == '\0')
       return(strin);

	/* Verifica si existe parentesis que no balancean */
    for (i=0; *(strin+i) != '\0'; i++)
	{
		if ( *(strin+i) == '(' )
			count++;
		else
		{
			if ( *(strin+i) == ')' )
			{
				count--;

				/* No permite que exista un ) antes que un ( */
				if ( count < 0 )
				{
					*(strin+i) = ' ';

					count = 0;
				}
			}	
		}
	}
	
	/* Ajusta los parentesis si count es positivo */
	if ( count > 0 )
	{
		/* Ajusto los parentesis insertando al final ) */
		for ( j = 0; j < count; j++ )
		{
			for ( i = strlen( strin ) - 1; i >= 0 ; i-- )
			{
				if ( *(strin+i) != ')' )
				{
					if ( *(strin+i) != '(' )
						*(strin+i) = ')';
					else
						*(strin+i) = ' ';

					break;
				}
			}
		}
	}

    return(strin);
}
