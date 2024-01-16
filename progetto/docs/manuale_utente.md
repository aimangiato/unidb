# Manuale utente

Di seguito sono riportate le istruzioni tecniche per lanciare la piattaforma "UniDB" per la gestione degli esami universitari.

### Requisiti
* PHP in versione aggiornata (version 8 o più)
* PostgreSQL in versione aggiornata (version 15 o più)
* Un WebServer; lavorando su windows, ho utilizzato Xampp
* Un software per la creazione, gestione e il monitoraggio dei database. Ho utilizzato PgAdmin4, ma potenzialmente se ne possono utilizzare altri o anche la shell PSQL
  
---

*Tutti i software sono stati testati e sviluppati su Windows; l'installazione e il funzionamento dei componenti potrebbe differire su altri sistemi operativi.*

---

Nota bene: PHP potrebbe non riconoscere le funzioni di connessione e manipolazione degli oggetti di Postgres. In quel caso, è necessario de-commentare il pacchetto di funzioni pgsql dal file php.ini situato nella cartella di installazione di PHP.

### Database PostgreSQL

* Per creare il database sul proprio ambiente, è necessario importare i dump sia degli schemas nel file **database/dump-schema-only.sql** , che dei dati nel file **database/dump-data-only.sql**

* Per comunicare con il database *unidb* ho utilizzato la funzione open_pg_connection() nel file funzioni.php. quest'ultima utilizza le credenziali dichiarate nel file conf.php per stabilire una connessione con il database. E' buona pratica terminare la connessione al termine di ogni utilizzo con la funzione close_pg_connection($database):

```php
/*
Open connection with PostgreSQL server
*/
function open_pg_connection() {
	include_once('conf.php');
    
    $connection = pg_connect("host=".myhost." port=".myport." dbname=".mydb." user=".myuser." password=".mypsw);

    if (!$connection) {
        echo "Errore durante il tentativo di connessione al server";
        exit;
    }
    
    return $connection;
    
}

/*
Close connection with PostgreSQL server
*/
function close_pg_connection($db) {
        
    return pg_close ($db);
    
}
```

---

## Lanciare Unidb
* Creare una cartella in **C:\xampp\htdocs** e importare in essa tutti i file presenti nella directory soprastante a questa
* Avviare il WebServer
* Su un browser, digitare il link **localhost/nome_cartella_di_unidb**. Questo vi reindirizzerà automaticamente a **index.php** che è la pagina di log-in. 




