## Task - 1: Compose SQL to grab book-related data based on some criterias

SELECT 
users.id, 
concat(users.first_name,' ', users.last_name) as `Name`,  
GROUP_CONCAT(books.name) as `Books`, 
books.author as `Author`

FROM users 
JOIN user_books on user_books.user_id = users.id 
JOIN books on user_books.book_id = books.id 

WHERE users.age BETWEEN 7 AND 17  
GROUP by users.id, books.author having COUNT(distinct books.author) = 1 and COUNT(user_books.id) = 2;



## Task - 2: Laravel API for currencies

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
I've attached the "API Request.paw" file in the root project dir with ready-to use API methods.

![alt text](paw.png)

