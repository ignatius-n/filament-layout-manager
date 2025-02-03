import Sortable from 'sortablejs';

let sortableInstance;

const moveEndMorphMarker = (el) => {
    const endMorphMarker = Array.from(el.childNodes).filter((childNode) => {
        return childNode.nodeType === 8 && ['[if ENDBLOCK]><![endif]', '__ENDBLOCK__'].includes(childNode.nodeValue?.trim());
    })[0];

    if (endMorphMarker) {
        el.appendChild(endMorphMarker);
    }
}

function initializeSortable() {
    const grid = document.querySelector('[x-ref="grid"]');
    if (grid) {
        sortableInstance = new Sortable(grid, {
            animation: 150,
            handle: '.handle',
            ghostClass: 'opacity-50',
            onEnd: (evt) => {
                const orderedIds = Array.from(grid.children).map(el => el.dataset.id);
                moveEndMorphMarker(grid);
                Livewire.dispatch('updateLayout', { orderedIds: orderedIds });
            }
        });
    }
}



// Initialize on load
initializeSortable();

// Reinitialize whenever Livewire re-renders
document.addEventListener('livewire:update', function() {
    if (Livewire.getByName('editMode')) {
        initializeSortable();
    } else {
        sortableInstance?.destroy();
    }
});
