var xhr;
(function($){
    $(function(){
        $(document)
            .on('click', ".alpixel_modal_button", function(e) {
                e.preventDefault();

                var icon   = $(this).find('i');
                var modal  = $($(this).attr('data-target'));

                icon.removeClass('fa-plus-circle')
                    .addClass('fa-spinner fa-pulse');

                updateModal(modal, null, icon);
            })
        ;
    });

    function updateModal(modal, values, icon) {
        if(xhr != undefined)
            xhr.abort();

        xhr = $.post(modal.attr('data-source'), values, function(data){

            if(data.submitted == true && data['errors'] == 0) {
                document.location.reload();
            } else {
                if(icon != undefined) {
                    icon.addClass('fa-plus-circle');
                    icon.removeClass('fa-spinner fa-pulse');
                }

                modal.find('.modal-dialog').html(data.html);
                modal.find('form').on('submit', function(e){
                    e.preventDefault();
                    updateModal(modal, $(this).serialize());
                });

                if(modal.is(':visible')) {
                    remoteCallback(modal);
                } else {
                    modal.one('shown.bs.modal', function(){
                        remoteCallback(modal);
                    });
                    modal.modal('show');
                }
            }
        });
    }

    function remoteCallback(modal) {
        modal.trigger('modal:updated');
    }
})(jQuery);
