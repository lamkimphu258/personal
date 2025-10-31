I need to fix a malformed quote issue in the controller where the x-text attribute has incorrect escaping. The line needs:

 sed -i "s/x-text=.*completed.*Incomplete/x-text='completed ? \"Completed\" : \"Incomplete\"'/" app/Http/Controllers/TodoController.php