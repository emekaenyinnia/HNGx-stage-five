# Chrome Extension Api

1. Upload Media : `/api/upload`
      - **Method:** POST
    - **Request Body:**
      - media (video required) - image src
    ```json
      {
        "media": top_falls.3gp,
      }
    ```
   - **Response :**

   ```json
       {
         "message": "Media uploaded successfully.",
         "url": "api/screen_record_894728729"
       }
   ```

## Get Media

2. Get Media: `/api/:filename`
   - **Method:** GET
   - **Parameters**:
     - `filename` (path parameter, string) - The filename of the video to Get eg screen_record_894728729 .
   - **Request Body: None**
   -  **Response Body: None**



   ```

