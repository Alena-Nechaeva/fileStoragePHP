# API Documentation

| Method | Route                                      | Short Description                           | Formdata/JSON | Body Fields                      |
| ------ | ------------------------------------------ | ------------------------------------------- | ------------- | -------------------------------- |
| POST   | `/registration`                            | Register a new user                         | JSON          | `email`, `password`, `name`      |
| POST   | `/login`                                   | Log in a user                               | JSON          | `email`, `password`              |
| POST   | `/reset-password`                          | Reset user password                         | JSON          | `email`                          |
| GET    | `/logout`                                  | Log out a user                              | None          | None                             |
| PUT    | `/update-me`                               | Update own user profile                     | JSON          | `email`, `name`                  |
|        |                                            |                                             |               |                                  |
| GET    | `/users`                                   | Admin: Get list of all users                | None          | None                             |
| GET    | `/users/{id}`                              | Admin: Get specific user details by ID      | None          | None                             |
| PUT    | `/users/update/{id}`                       | Admin: Update a specific user by ID         | JSON          | `name`, `email`                  |
| DELETE | `/users/delete/{id}`                       | Admin: Delete a specific user by ID         | None          | None                             |
|        |                                            |                                             |               |                                  |
| GET    | `/files`                                   | Get list of user’s files                    | None          | None                             |
| GET    | `/files/{id}`                              | Get specific file details by ID             | None          | None                             |
| POST   | `/files/add`                               | Upload a new file                           | Formdata      | `file`, `directory_id`(optional) |
| PUT    | `/files/rename`                            | Rename a file                               | JSON          | `file_id`, `new_name`            |
| DELETE | `/files/delete/{id}`                       | Delete a file by ID                         | None          | None                             |
|        |                                            |                                             |               |                                  |
| GET    | `/directories`                             | Get list of directories                     | None          | None                             |
| GET    | `/directories/{id}`                        | Get files within a specific directory       | None          | None                             |
| POST   | `/directories/add`                         | Add a new directory                         | JSON          | `name`, `parent_id`              |
| PUT    | `/directories/rename`                      | Rename a directory                          | JSON          | `directory_id`, `new_name`       |
| DELETE | `/directories/delete/{id}`                 | Delete a directory by ID                    | None          | None                             |
|        |                                            |                                             |               |                                  |
| POST   | `/search-user`                             | Search for a user by email                  | JSON          | `email`                          |
| GET    | `/files/shared-with-me`                    | Get files shared with the logged-in user    | None          | None                             |
| GET    | `/files/shared-with-me/{file_id}`          | Get a specific file shared with the user    | None          | None                             |
| GET    | `/files/shared-with-others/{file_id}`      | Get list of users who have access to a file | None          | None                             |
| PUT    | `/files/share/{file_id}/{user_id}`         | Share a file with another user              | None          | None                             |
| DELETE | `/files/remove-access/{file_id}/{user_id}` | Revoke file access from a user              | None          | None                             |
| GET    | `/files/download/{file_id}`                | Download a file by ID                       | None          | None                             |
