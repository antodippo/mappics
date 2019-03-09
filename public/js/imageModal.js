
$('#imageModal').on('show.bs.modal', function (event) {
    var relatedTarget = $(event.relatedTarget);
    var imageUrl = relatedTarget.data('image-modal-url');
    $(this).find('.modal-content').load(imageUrl);
});

$('#imageModal').on('hidden.bs.modal', function (event) {
    $(this).find('.modal-content').text(null);
});