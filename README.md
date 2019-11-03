**API DOCS**

1) login

   url: https://api.ngost.by/api/login_check
   
       data type: JSON,
       method: POST
       data: 
        {
           "username" : String username or email,
           "password" : String password
        }
 2) register
 
    url: https://api.ngost.by/register
    
        data type: JSON
        method: POST
        data: 
         {
            "username": String "root",
            "password": String "root",
            "email" : String "root@ngost.by",
            "phone": String "+375297555555"
         }
         
 **Ко всем остальным запросам крепить заголовок** 
    
     header: 
      {
        Authorization: "Bearer " + token
      }
  1) News
   
   1.1 Create news
   
          url: https://api.ngost.by/api/news
      
          data type: JSON
          method: POST
          data: 
           {
              "title": String,
              "description": Text,
              "image" : File,
              "short_desc" : String
           }
           
   1.2 Get all news
      
           url: https://api.ngost.by/api/news
        
           method: GET
             
   1.3 Get new by id
         
           url: https://api.ngost.by/api/news/{id}
           
           method: GET
                
   1.4 DELETE new by id
            
           url: https://api.ngost.by/api/news/{id}
              
           method: DELETE
             
   1.5 UPDATE new by id
                
           url: https://api.ngost.by/api/news/{id}/edit
                  
           data type: JSON
           method: POST
           data: 
           {
              "title": String,
              "description": Text,
              "image" : File,
              "short_desc" : String
           }