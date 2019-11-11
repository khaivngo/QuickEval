<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
  </head>
  <body>
    <h1>A new QuickEval sign up has requested scientist permissions.</h1>

    <p>{{ $user->email }}</p>
    <p>{{ $user->name }}</p>

    <a href="https://quickeval.no/admin">Login to accept/reject request</a>
  </body>
</html>
