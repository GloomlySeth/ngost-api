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
         
 **Все гет запросы могут иметь пагинацию, данные отправлять гет параметрами**
         
       sort_field = cteated_at
       limit = 15
       offset = 1
         
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
                   
       2.2 Show media files
                                 
                   url: https://api.ngost.by/api/media
            
                   method: GET
                     
   3) Requirements 
      
       3.1 Create Requirements
         
             url: https://api.ngost.by/api/requirements
             data type: JSON
             method: POST
             data: 
             {
                "fields": Array,
             }
                     
       3.2 Get all Requirements
           
             url: https://api.ngost.by/api/requirements
             method: GET
                   
       3.3 Get Requirements by id
               
             url: https://api.ngost.by/api/requirements/{id}       
             method: GET
                      
       3.4 DELETE Requirements by id
                  
             url: https://api.ngost.by/api/requirements/{id}
             method: DELETE
                           
       3.5 UPDATE Requirements by id
                      
             url: https://api.ngost.by/api/requirements/{id}/edit               
             data type: JSON
             method: POST
             data: 
             {
                 "fields": Array,
             }
                 
   4) User request
   
        4.1 Get all user requests
        
              url: https://api.ngost.by/api/user/requests
              method: GET
              
        4.2 Create user request
                
              url: https://api.ngost.by/api/user/requests
              method: POST
              data type: JSON
              data: 
              {
                  "requirement": Int (id requirement),
                  "file": Int (id file)
              }
         
        4.3 Get user request by ID
                
              url: https://api.ngost.by/api/user/requests/{id}
              method: GET
         
        4.4 Update user request by ID
                        
              url: https://api.ngost.by/api/user/requests/{id}
              method: POST
              data type: JSON
              data: 
              {
                 "requirement": Int (id requirement),
                 "file": Int (id file)
              }
                    
        4.5 Update user request by ID
                        
              url: https://api.ngost.by/api/user/requests/status/{id}
              method: POST
              data type: JSON
              data: 
              {
                 "status": String
              }
          
        4.6 Delete user request by ID
                        
              url: https://api.ngost.by/api/user/requests/{id}
              method: DELETE
              
   5) Media
     
        5.1 Create file
                          
               url: https://api.ngost.by/api/files
                            
               data type: x-www-form-urlencoded
               method: POST
               data: 
               {
                    "file": File
               }
        5.2 Show file
                             
               url: https://api.ngost.by/api/files/{id}
        
               method: GET
             
   6) Pages
      
          1.1 Create pages
          
                 url: https://api.ngost.by/api/pages
             
                 data type: JSON
                 method: POST
                 data: 
                  {
                     "title": String,
                     "name": String,
                     "header": String,
                     "content": Array,
                     "status": String
                  }
                  
          1.2 Get all pages
             
                  url: https://api.ngost.by/api/pages
                  security: false
                  method: GET
                    
          1.3 Get page by slug
                
                  url: https://api.ngost.by/api/pages/{slug}
                  
                  method: GET
                       
          1.4 DELETE page by id
                   
                  url: https://api.ngost.by/api/pages/{id}
                     
                  method: DELETE
                    
          1.5 UPDATE pages by id
                       
                  url: https://api.ngost.by/api/pages/{id}/edit
                         
                  data type: JSON
                  method: POST
                  data: 
                  {
                     "title": String,
                     "name": String,
                     "header": String,
                     "content": Array,
                     "status": String
                  }
                  
                  
   7) Options
         
             1.1 Create options
             
                    url: https://api.ngost.by/api/options
                
                    data type: JSON
                    method: POST
                    data: 
                     {
                        "title": String,
                        "options": Array
                     }
                     
             1.2 Get all options
                
                     url: https://api.ngost.by/api/options
                     method: GET
                       
             1.3 Get page by slug
                   
                     url: https://api.ngost.by/api/options/{slug}
                     
                     method: GET
                          
             1.4 DELETE option by slug
                      
                     url: https://api.ngost.by/api/options/{slug}
                        
                     method: DELETE
                       
             1.5 UPDATE option by slug
                          
                     url: https://api.ngost.by/api/options/{slug}/edit
                            
                     data type: JSON
                     method: POST
                     data: 
                     {
                       "title": String,
                       "slug": String,
                       "options": Array
                     }