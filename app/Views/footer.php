</div>
<!-- page-body-wrapper ends -->
</div>

<script>
    $(document).ready(function() {
        function showTime() {
            var now = moment.tz("Asia/Taipei");
            var formattedDate = now.format("MMM DD, YYYY hh:mm A");

            document.getElementById("MyClockDisplay").innerText = formattedDate;
            document.getElementById("MyClockDisplay").textContent = formattedDate;


            setTimeout(showTime, 1000);
        }

        // Call the showTime() function to start displaying the time
        showTime();
        $('#clear').on('click', function() {
            $('.form-control').each(function(index, element) {
                $(element).val('');
                console.log(element);
            });
            $('#limit').val(8);
            $('#frmFilter').submit();
        });

        $('#limit').on('change', function() {
            $('#frmFilter').submit();
        });

        $('#logout').on('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                text: 'Are you sure you want to logout?',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "GET",
                        url: "<?php echo site_url('logout') ?>",
                        dataType: "json",
                        success: function(response) {
                            if (response.islogout) {
                                window.location.href = response.url
                            } else {
                                Swal.fire({
                                    title: 'Warning',
                                    icon: 'warning',
                                    text: response.msg,
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'Ok'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = response.url
                                    }
                                })
                            }
                        }
                    });
                }
            })


            $('input[type=number][max]:not([max=""])').on('input', function(ev) {
                var $this = $(this);
                var maxlength = $this.attr('max').length;
                var value = $this.val();
                if (value && value.length >= maxlength) {
                    $this.val(value.substr(0, maxlength));
                }
            });

        });

        $('#getTransID').on('click', function() {
            var transID = $('#transID').val();
            if (transID) {
                $.ajax({
                    type: "POST",
                    url: '<?php echo site_url('getTransID') ?>',
                    data: {
                        transID: transID
                    },
                    dataType: "JSON",
                    beforeSend: function() {
                        $('#getTransID').attr('disabled', true);
                    },
                    success: function(response) {
                        if (response['success'] === true) {
                            window.location.replace(response['url']);
                        } else {
                            Swal.fire({
                                title: response['msg'],
                                icon: 'warning',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Ok'
                            })
                        }

                        $('#getTransID').attr('disabled', false);
                    }
                });
            } else {
                Swal.fire({
                    title: 'Invalid Series No.',
                    icon: 'warning',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok'
                })
            }
        });
    });

    $('#transID').on('keypress', function(e) {
        var transID = $('#transID').val();
        if (e.which == 13) {
            if (transID) {
                $.ajax({
                    type: "POST",
                    url: '<?php echo site_url('getTransID') ?>',
                    data: {
                        transID: transID
                    },
                    dataType: "JSON",
                    beforeSend: function() {
                        $(this).attr('disabled', true);
                    },
                    success: function(response) {
                        if (response['success'] === true) {
                            window.location.replace(response['url']);
                        } else {
                            Swal.fire({
                                title: response['msg'],
                                icon: 'warning',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Ok'
                            })
                        }

                        $(this).attr('disabled', false);
                    }
                });
            } else {
                Swal.fire({
                    title: 'Invalid Series No.',
                    icon: 'warning',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok'
                })
            }
        }
    });

    function sorting(fld) {
        if ($('#sortby').val() == fld) {
            if ($('#sortorder').val() == 'asc') {
                $('#sortorder').val('desc');
            } else {
                $('#sortorder').val('asc');
            }
        } else {
            $('#sortby').val(fld);
            $('#sortorder').val('asc');
        }
        $('#frmFilter').submit();
    }

    // $(".select2-search-disable").select2({
    //     minimumResultsForSearch: -1,
    // });
</script>
</body>

</html>