<!-- Bootstrap notify -->
<script src="{{ asset(config('admin_config.theme_name')) }}/plugins/bootstrap-notify/bootstrap-notify.min.js" type="text/javascript"></script>


{{-- Bootstrap Notifications using Prologue Alerts --}}
<script type="text/javascript">
    jQuery(document).ready(function($) {

        @foreach (Alert::getMessages() as $type => $messages)
        @foreach ($messages as $message)

        $.notify({
        	message: "{!! str_replace('"', "'", $message) !!}"
        },{
        	type: "{{ $type }}"
        });

        @endforeach
        @endforeach
    });
</script>