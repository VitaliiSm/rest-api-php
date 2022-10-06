# rest-api-php
Register user
 /api/register POST
{
    email: string,
    password: string
}

login
 /api/register POST
{
    email: string,
    password: string
}


copy token in Headers
 {
    Authorization: Bearer token,
 }

check validation
 /api/auth GET


create todo
 /api/todo POST
form-data
{
    category_id: integer,
    description: string,
    important: integer,
    file: 
}

get all todo
 /api/todo GET

delete todo
 /api/todo DELETE
{
    id:integer
}

update todo
/api/todo PUT
{
    category_id: integer,
    description: string,
    important: integer
}

change status
/api/todo PATCH
{
    status: integer
}
