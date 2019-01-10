<p>
<a href="https://travis-ci.org/tomopongrac/popularity-score-symfony"><img src="https://travis-ci.org/tomopongrac/popularity-score-symfony.svg" alt="Build Status"></a></p>

## Opis aplikacije

Ovo je sustav koji računa popularnost određene riječi. Sustav za zadanu riječ pretražuje servis providera i na osnovu broja pozitivnog i negativnog rezultata računa ocjenu popularnosti zadane riječi od 0-10 (rezultat će biti zaokružen na dvije decimale).

## Postavljanje projekta na lokalni server

Server mora zadovoljiti sljedeće zahtjeve:

* PHP >= 7.2

Klonirajte repozitorij

    git clone https://github.com/tomopongrac/popularity-score-symfony.git

Prebacite se u direktorij repozitorija
 
     cd popularity-score-symfony

Instalirajte sve komponente aplikacije

    composer install

Kopirajte datoteku env.homestead i eventualne konfigacije promjene

    cp .env.local-template .env.local

Kreirajte bazu na lokalnom serveru

    ./bin/console doctrine:schema:create

Morate kreirati ključeve kako biste mogli kreirati token za pristup aplikaciji

	./bin/console app:oauth:install
	./bin/console app:oauth-client:create

Kopirajte u .env.local datoteku Client ID i Client Secret koji ste dobili iz prethodne naredbe

    CLIENT_ID=<Client ID>
    CLIENT_SECRET=<Client Secret>

Pokrenite lokalni server

	./bin/console server:run

Ukoliko se link lokalnog servera drugačiji od http://127.0.0.1:8000 onda ga ažurirajte u .env.local datoteci

	APP_URL=http://127.0.0.1:8000

## OAuth
Korišten je osnovni OAuth2 sustav bez korisnika (samo client credentials) preko paketa [trikoder/oauth2-bundle](https://github.com/trikoder/oauth2-bundle).

## Korištenje aplikacije

Možete koristiti aplikaciju [Postman](https://www.getpostman.com) ili možete koristiti konzolu.

API koristi client credentials autorizaciju pa prvo morate kreirati token pomoću komande

    ./bin/console app:oauth-token:create

Kako biste dobili popularnost riječi “php”, u konzolu upišite naredbu gdje umjesto \<PASTE TOKEN> upisujete token koji ste dobili iz prethodne naredbe

    curl -H "Authorization: Bearer <PASTE TOKEN>" http://127.0.0.1:8000/score\?term\=php    

Rezultat će biti (vrijednost score može biti drugačija s obzirom na trenutnu popularnost tražene riječi na provideru)

    {
             "term": "php",
             "score": 3.39
    }

Ukoliko tražimo riječ koja ne postoji s naredbom

    curl -H "Authorization: Bearer <PASTE TOKEN>" http://127.0.0.1:8000/score\?term\=abcdxyz

Dobivamo rezultat

    {
             "term": "abcdxyz",
             "score": 0
    }
 
 Za korištenje verzije 2 u link dodajemo v2
 
     curl -H "Authorization: Bearer <PASTE TOKEN>" http://127.0.0.1:8000/score/v2\?term\=php

gdje je rezultat u JSONAPI specifikaciji

    {
        "data": {
            "term": "php",
            "score": 3.39
        }
    }

## Kreiranje novog providera
Za kreiranje novog providera potrebno je kreirati novu klasu u direktoriju src/Services koja nasljeđuje klasu ServiceProvider u istom direktoriju.

U novoj klasi se moraju kreirati dvije metode:

* getResult()
* getCount()

## Zamjena providera
Ukoliko želite promijeniti providera to ćete napraviti na način da promjenite trenutnog providera (GitHubServiceProvider) u config datoteci config/services.yaml

    App\Services\ServiceProviderInterface: ’@App\Services\GitHubServiceProvider’

## Kreiranje nove verzije API-ja
Za kreiranje nove verzije potrebno je kreirati klasu u direktoriju src/Responses naziva JsonResponseV{broj} koja implementira interaface ResponseInterface.

U novoj klasi se moraju kreirati dvije metode:

* tranformValidationData()
* transformNormalData()
* getResponseHeader()