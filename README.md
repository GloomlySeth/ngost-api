**API DOCS**

1) login_check

   url: https://api.ngost.by/api/login_check
   
       data type: JSON,
       method: POST
       data: 
        {
           "username" : string Username,
           "password" : string Password
        }
 2) url: https://api.ngost.by/register
    
        data type: JSON
        method: POST
        data: 
         {
            "username": String "root",
            "password": String "root",
            "email" : String "root@ngost.by",
            "phone": String "+375297555555"
         }