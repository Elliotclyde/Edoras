<head>
    <title>Posts</title>
    <link rel="stylesheet" href="/styles.css">
    <script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
</head>
<body>
<h1>Edit post</h1>
<form action="/backend/posts/<?=$id?>/edit" method="post">
<label for="title">Title</label><input id="title" type="text" name="title" value="<?=$title?>">
<label for="body">Body</label><textarea id="body" rows="10" cols="70" name="body"><?=$body?></textarea>
<label for="slug">Slug</label><input id="slug" type="text" name="slug" value="<?=$slug?>">
<input type="submit">
</form>

<a href="/backend/posts">Back</a>

<button id="delete-button">Delete</button>

<script>

CKEDITOR.replace('body');

document.getElementById("delete-button").addEventListener("click",onDeleteButtonClick);

function onDeleteButtonClick(){
    document.querySelector("body").innerHTML +=
    "<div id=\"delete-container\"><p>Are you sure?<p><button id=\"confirm-delete-button\">Yes</button>"
    + "<button id=\"revert-delete-button\">No</button></div>";
    addDeleteListeners();

}

function onDeleteconfirm(){
    var xhr = new XMLHttpRequest();
    xhr.open('DELETE', "/backend/posts/<?=$id?>", true);
    xhr.onreadystatechange = function() {
        if (this.status == 200 && this.readyState == 4) {
            redirectToPosts();
        }
        else{throw new Exception("error occured");}
    }
    xhr.send();
}

function redirectToPosts(){
    window.location.replace("/backend/posts");
}

function onRevertDelete(){
    var container = document.querySelector("#delete-container");
    container.parentNode.removeChild(container);
    document.getElementById("delete-button").addEventListener("click",onDeleteButtonClick);
}

function addDeleteListeners(){
    document.getElementById("confirm-delete-button").addEventListener("click",onDeleteconfirm);
    document.getElementById("revert-delete-button").addEventListener("click",onRevertDelete);

}
</script>
</body>