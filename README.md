# conversation_api
Conversation api in lavarel


To run the api correctly you have to start a lavarel serve using the command: php artisan serve, and have a mysql instance running, you can configure the MYSQL in the .env file at root.
The default settings for .env are the following, configure it for your own purposes:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=conversational_db
DB_USERNAME=root
DB_PASSWORD=

To test the app you have to make a post to this url: http://localhost:8000/api/conversation where port 8000 is the port for the lavarel serve.
The body have to be as the following:
{
  "user_id":1, //any number, the api will remember the conversation
  "query": "text" //your question or prompt
}
