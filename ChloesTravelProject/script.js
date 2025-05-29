let showModalImages = () => {
    let modal = document.getElementById("image_modal");
    modal.style.display = "block";
    document.getElementById("backdrop").style.display = "block";

    modal_header =
        '<img src="images/Avatars/chooseAvatar.jpg" alt="Sock Thieves" width="100px"> <br> CHOOSE AVATAR <span class="close cursor" onclick="closeImageModal()">&times;</span> <br> <div class ="modal-content">'
}

modal_content = '<div class="image-grid" id="imageGrid"></div>';

modal.innerHTML = modal_header + modal_content;

const imageGrid = document.getElementById("imageGrid");


for (let i = 0; i < 20; i++) {
    const imageDiv = document.createElement("div");
    const img = document.createElement("img");
    img.src = "images\\Avatars\\avatar" + (i + 1) + ".jpg"
}

//Adding onclick event to the image
img.onclick = function () {
    //Make a request to the PHP script to set a cookie
    fetch("set_avatar_cookie.php?avatar=" = (i + 1))
        .then(response => response.text())
        .then(data => {
            document.getElementById("avatarimage").innerHTML = data;

        })
        .catch(error => console.error("Error:", error));
};

