function openLightbox(album) {
    fetch(`?album=${album}`)
        .then(res => res.text())
        .then(data => {
            document.getElementById("lightbox-content").innerHTML = data;
            document.getElementById("lightbox").style.display = "block";
            document.getElementById("album-grid").style.display = "none";
        });
}

function closeLightbox() {
    document.getElementById("lightbox").style.display = "none";
    document.getElementById("lightbox-content").innerHTML = '';
    document.getElementById("album-grid").style.display = "flex";
}
