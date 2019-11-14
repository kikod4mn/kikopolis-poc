const panels = document.querySelectorAll('.panel-5');

function toggleOpen() {
    panels.forEach(panel => {
        panel.classList.toggle('faded');
        if (panel.classList.contains('open') && panel !== this) {
            panel.classList.toggle('open');
        }
        if (panel === this) {
            this.classList.toggle('open');
            this.classList.remove('faded');
        }
    });
}

function toggleActive(e) {
    if (e.propertyName.includes('flex')) {
        this.classList.toggle('open-active');

    }
}
panels.forEach(panel => panel.addEventListener('click', toggleOpen));
panels.forEach(panel => panel.addEventListener('transitionend', toggleActive));