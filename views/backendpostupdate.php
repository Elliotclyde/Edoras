<head>
    <title>Posts</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    
<h1>Edit post</h1>
<form action="/backend/posts/edit/<?=$id?>" method="post">
<label for="title">Title</label><input id="title" type="text" name="title" value="<?=$title?>">
<label for="body">Body</label><textarea id="body" rows="10" cols="70" name="body"><?=$body?></textarea>
<label for="slug">Slug</label><input id="slug" type="text" name="slug" value="<?=$slug?>">
<input type="submit">
</form>

<a href="/backend/posts/new"></a>

</body>