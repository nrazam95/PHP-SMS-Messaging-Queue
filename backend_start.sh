# export BACKEND_HOST value in this file and make variables
# available to the script
export $(cat .env | xargs)
# then make as variable
echo $BACKEND_HOST
echo $BACKEND_PORT
# start the server

# start the server
php -S $BACKEND_HOST:$BACKEND_PORT server.php