
## Laravel API for currencies

###Steps to start server:

1. Install required packages
composer install

2. Run sail to start docker container:  
./vendor/bin/sail up -d

3. Migrate DB
./vendor/bin/sail artisan migrate

4. Open http://127.0.0.1:8000/

### How to work with API
1. Register new user:

    POST http://127.0.0.1:8000/api/v1/register
    
        name 
        email 
        password

2. Login and get the token

    POST http://127.0.0.1:8000/api/v1/login
    
        email 
        password

    In the response you will get a token (if successful login): 
    ZKXFPUBF_sjDcQekwpmTdVzbLAVSOPNRcGCLanodMHziJeyNIAqvrDWGQSvqxhmk
    
3. Use that token in the header to access API:

    Authorization: Bearer ftJebToMbkKsOpXiNmIZkWEHSP_OhRgHzULGEwtDDdpldaLUYW_nwGjnacyqjlXx

### API methods:
1. Get currency info (the currency value could be empty or separated by comma)

    GET /api/v1/rates?currency=USD,RUB,EUR

2. Convert currency 

    POST /api/v1/convert
    
        currency_from
 
        currency_to
 
        value 

### PAW file
I've attached the "API Request.paw" file with ready-to use API methods.

![alt text](paw.png)

