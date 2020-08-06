<head>
    <title>Posts</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    
<h1>New Post</h1>
<form action="/backend/posts/new" method="post">
<label for="title">Title</label><input id="title" type="text" name="title" value="">
<label for="body">Body</label><textarea id="body" rows="10" cols="70" type="text" name="body" value=""></textarea>
<label for="slug">Slug</label><input id="slug" type="text" name="slug" value="">
<input type="submit">
</form>

<a href="/backend/posts/new"></a>

</body>