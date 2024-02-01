<!-- Confirm Payment Modal -->
<div id="payment-confirm-modal" class="modal fade">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">{{ translate('Confirm Payment')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mt-1">{{ translate('are_you_sure_confirm_payment')}}</p>
                <button type="button" id="payment-cancel-link" class="btn btn-link mt-2" data-dismiss="modal">{{ translate('Cancel')}}</button>
                <a href="" id="payment-confirm-link" class="btn btn-primary mt-2" onclick="disable_button()">{{ translate('Confirm')}}</a>
            </div>
        </div>
    </div>
</div><!-- /.modal -->

<script>
        function disable_button() {
            document.getElementById('payment-confirm-link').disabled = true;
            document.getElementById('payment-cancel-link').disabled = true;

            // Aquí puedes ocultar el modal después de deshabilitar los botones
            $('#payment-confirm-modal').modal('hide');
        }
</script>