
// Profile Image Handler
const profileImgEditBtn = document.querySelector("#profileImgEditBtn");
const profileUpload = document.querySelector("#profileUpload");

const profileModal = new bootstrap.Modal(document.querySelector("#profileImgModal"), {
    keyboard: false
})

profileImgEditBtn.addEventListener("click", () => {
    profileModal.show();
});

// Croppie
const croppieEl = document.querySelector('#croppie');
const vanilla = new Croppie(croppieEl, {
    viewport: { width: 200, height: 200, type: 'circle' },
    boundary: { width: 300, height: 300 },
    showZoomer: true,
});

profileUpload.addEventListener("change", () => {
    vanilla.bind({
        url: URL.createObjectURL(profileUpload.files[0]),
    });
})

