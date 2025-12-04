const overlay = document.querySelector('#overlay360');

const butOverlay = document.querySelector('#butOverlay');
if (butOverlay) {
    butOverlay.addEventListener('click', _ => {
        overlay.classList.remove('hidden');
    })
}

const cross = overlay.querySelector('button');
cross.addEventListener('click', _ => {
    overlay.classList.add('hidden');
})
