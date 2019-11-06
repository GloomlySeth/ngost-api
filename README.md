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
 
    url: https://api.ngost.by/api/register
    
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
              "image" : Integer id image,
              "short_desc" : String
           }
           
   1.2 Get all news
      
           url: https://api.ngost.by/api/news
           security: false
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
              "image" : Integer id image,
              "short_desc" : String
           }
           
  2) Media
  
   2.1 Create media file
                   
              url: https://api.ngost.by/api/media
                     
              data type: x-www-form-urlencoded
              method: POST
              data: 
              {
                 "file": File
              }
   2.2 Show media file
                      
               url: https://api.ngost.by/api/media/{id}
 
               method: GET
                 
  3) Requirements 
  
  1.1 Create Requirements
     
     url: https://api.ngost.by/api/requirements
     data type: JSON
     method: POST
     data: 
     {
        "fields": Array,
     }
             
  1.2 Get all Requirements
       
     url: https://api.ngost.by/api/requirements
     method: GET
               
  1.3 Get Requirements by id
           
     url: https://api.ngost.by/api/requirements/{id}       
     method: GET
                  
  1.4 DELETE Requirements by id
              
     url: https://api.ngost.by/api/requirements/{id}
     method: DELETE
                
               
  1.5 UPDATE Requirements by id
                  
     url: https://api.ngost.by/api/requirements/{id}/edit               
     data type: JSON
     method: POST
     data: 
     {
         "fields": Array,
     }
             