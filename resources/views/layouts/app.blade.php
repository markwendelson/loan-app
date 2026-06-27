<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="pixelstrap">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{url('assets/images/favicon.png')}}" type="image/x-icon">
    <link rel="shortcut icon" href="{{url('assets/images/favicon.png')}}" type="image/x-icon">
    <title>{{config('app.name')}} - @yield("title")</title>

    <!-- Google font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;300;400;500;600;700;800&amp;display=swap"
        rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{url('assets/css/font-awesome.css')}}">

    <!-- ico-font-->
    <link rel="stylesheet" type="text/css" href="{{url('assets/css/vendors/icofont.css')}}">

    <!-- Themify icon-->
    <link rel="stylesheet" type="text/css" href="{{url('assets/css/vendors/themify.css')}}">

    <!-- Flag icon-->
    <link rel="stylesheet" type="text/css" href="{{url('assets/css/vendors/flag-icon.css')}}">

    <!-- Feather icon-->
    <link rel="stylesheet" type="text/css" href="{{url('assets/css/vendors/feather-icon.css')}}">

    <!-- Plugins css start-->
    <link rel="stylesheet" type="text/css" href="{{ url('assets/css/vendors/slick.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('assets/css/vendors/slick-theme.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('assets/css/vendors/scrollbar.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('assets/css/vendors/animate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ url('/assets/css/vendors/datatables.css') }}">
    <!-- Plugins css Ends-->

    <!-- Bootstrap css-->
    <link rel="stylesheet" type="text/css" href="{{url('assets/css/vendors/bootstrap.css')}}">

    <!-- App css-->
    <link rel="stylesheet" type="text/css" href="{{url('assets/css/style.css')}}">
    <link id="color" rel="stylesheet" href="{{url('assets/css/color-1.css')}}" media="screen">

    <!-- Responsive css-->
    <link rel="stylesheet" type="text/css" href="{{url('assets/css/responsive.css')}}">

    <!-- sweet alert -->
    <link rel="stylesheet" type="text/css" href="{{url('assets/css/vendors/sweetalert2.css')}}">

    <!-- common css -->
    {{--
    <link rel="stylesheet" type="text/css" href="{{ url('assets/css/common.css?v=' . uniqid()) }}"> --}}

    <link rel="stylesheet" type="text/css" href="{{ url('assets/css/loader.css') }}"/>

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <livewire:styles />

    @stack('css')
    <style>
        .form-group {
            margin-bottom: 10px;
        }

        .toast-index {
            z-index: 10000;
        }

        /* Chrome, Safari, Edge */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
        }

        /* Firefox */
        input[type="number"] {
        -moz-appearance: textfield;
        }
    </style>
</head>

<body>

    @include('components.failover-banner')

    <!-- loader starts-->
    <div class="loader-wrapper">
        <div class="loader">
            <div class="loader4"></div>
        </div>
    </div>
    <!-- loader ends-->

    <!-- tap on top starts-->
    <div class="tap-top"><i data-feather="chevrons-up"></i></div>
    <!-- tap on tap ends-->

    <!-- page-wrapper Start-->
    <div class="page-wrapper compact-wrapper" id="pageWrapper">

        @include('partials.header')

        <!-- Page Body Start-->
        <div class="page-body-wrapper">

            <div class="sidebar-wrapper" data-layout="stroke-svg">
                <div class="logo-wrapper"><a href="{{route('home')}}"><img class="img-fluid"
                            src="{{url('assets/images/logo/guiuan-logo.png')}}" alt=""></a>
                    <div class="back-btn"><i class="fa fa-angle-left"> </i></div>
                    <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="grid"> </i>
                    </div>
                </div>
                <div class="logo-icon-wrapper"><a href="{{route('home')}}"><img class="img-fluid"
                            src="{{url('assets/images/logo/guiuan-logo-small.png')}}" alt=""></a></div>
                <nav class="sidebar-main">
                    <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>

                    <div id="sidebar-menu">
                        <ul class="sidebar-links" id="simple-bar">
                            <li class="back-btn"><a href="{{route('home')}}"><img class="img-fluid"
                                        src="{{url('assets/images/logo/guiuan-logo-small.png')}}" alt=""></a>
                                <div class="mobile-back text-end"> <span>Back </span><i class="fa fa-angle-right ps-2"
                                        aria-hidden="true"></i></div>
                            </li>
                            <li class="pin-title sidebar-main-title">
                                <div>
                                    <h6>Pinned</h6>
                                </div>
                            </li>

                            @include('partials.sidebar')

                            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
                    </div>
                </nav>
            </div>

            <div class="page-body">
                @yield('content')
            </div>
        </div>
    </div>

    {{-- toast notification --}}
    <div class="toast-container position-fixed top-0 end-0 p-3 toast-index toast-rtl">
        <div class="toast hide toast fade " id="liveToast1" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex justify-content-between">
                <div class="toast-body" id="toastBody"></div>
                <button class="btn-close btn-close-white me-2 m-auto" type="button" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- latest jquery-->
    <script src="{{url('assets/js/jquery.min.js')}}"></script>
    <!-- Bootstrap js-->
    <script src="{{url('assets/js/bootstrap/bootstrap.bundle.min.js')}}"></script>
    <!-- feather icon js-->
    <script src="{{url('assets/js/icons/feather-icon/feather.min.js')}}"></script>
    <script src="{{url('assets/js/icons/feather-icon/feather-icon.js')}}"></script>
    <!-- scrollbar js-->
    <script src="{{url('assets/js/scrollbar/simplebar.js')}}"></script>
    <script src="{{url('assets/js/scrollbar/custom.js')}}"></script>
    <!-- Sidebar jquery-->
    <script src="{{url('assets/js/config.js')}}"></script>
    <!-- sweet alert -->
    <script src="{{url('assets/js/sweet-alert/sweetalert.min.js')}}"></script>
    <!-- Plugins JS start-->
    <script src="{{url('assets/js/datatable/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{url('assets/js/datatable/datatables/datatable.custom.js')}}"></script>
    <script src="{{url('assets/js/datatable/datatable-extension/dataTables.buttons.min.js')}}"></script>
    <script src="{{url('assets/js/datatable/datatable-extension/buttons.print.min.js')}}"></script>
    <script src="{{url('assets/js/sidebar-menu.js')}}"></script>
    <script src="{{url('assets/js/sidebar-pin.js')}}"></script>
    <script src="{{url('assets/js/slick/slick.min.js')}}"></script>
    <script src="{{url('assets/js/slick/slick.js')}}"></script>
    <script src="{{url('assets/js/header-slick.js')}}"></script>

    <!-- calendar js-->

    <!-- Plugins JS Ends-->
    <!-- Theme js-->

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script src="{{url('assets/js/script.js')}}"></script>

    <livewire:scripts />

    <script>
        const showToast = {
            show(message, className = 'text-bg-primary', delay = 5000) {
                const toastEl = document.getElementById('liveToast1');
                const toastBody = document.getElementById('toastBody');

                // Update content
                toastBody.innerHTML = message;

                // Update toast class
                toastEl.className = `toast align-items-center ${className} border-0`;

                // Create and show the toast
                const toast = new bootstrap.Toast(toastEl, { delay });
                toast.show();
            },

            success(message, delay = 3000) {
                this.show(message, 'text-bg-success', delay);
            },

            error(message, delay = 3000) {
                this.show(message, 'text-bg-danger', delay);
            },

            info(message, delay = 3000) {
                this.show(message, 'text-bg-info', delay);
            },

            warning(message, delay = 3000) {
                this.show(message, 'text-bg-warning', delay);
            }
        };



        function showAlert(response) {
            swal(
                response.title ?? '',
                response.message ?? '',
                response.type ?? ''
            );
        }

        function showModal(modalName) {
            $(`#${modalName}`).modal("show");
        }

        function hideModal(modalName) {
            clearFormInputs(modalName);
            $(`#${modalName}`).modal("hide");
        }

        function clearFormInputs(modalName) {
            document.querySelector(`#${modalName} form`).reset();
            $(`#${modalName} [name='id']`).val("");
        }

        function currencyFormat(amount, showDecimals = true) {
            return amount.toLocaleString('en-PH', {
                // style: 'currency',
                // currency: 'PHP',
                minimumFractionDigits: showDecimals ? 2 : 0,
                maximumFractionDigits: showDecimals ? 2 : 0
            });
        }

        @if(session('message'))
            showToast.success({!! json_encode(session('message')) !!});
        @endif

    </script>

    <script>

        // jQuery Combined Decimal Input Handler with Cursor Preservation
        $(document).on('input', '.decimal-only', function () {
            let cursorPosition = this.selectionStart;
            let originalValue = this.value;

            // Count non-comma characters before the cursor
            let nonCommaCountBeforeCursor = 0;
            for (let i = 0; i < cursorPosition; i++) {
                if (originalValue[i] !== ',') {
                    nonCommaCountBeforeCursor++;
                }
            }

            // Clean the overall value (only allow digits and a single dot)
            let cleaned = originalValue.replace(/[^0-9.]/g, '');
            let parts = cleaned.split('.');
            if (parts.length > 2) {
                cleaned = parts[0] + '.' + parts[1];
                parts = cleaned.split('.');
            }

            // Limit to 2 decimal places if there is a decimal point
            let hasDecimal = parts.length > 1;
            let integerPart = parts[0];
            let decimalPart = parts[1] || '';

            if (decimalPart.length > 2) {
                decimalPart = decimalPart.substring(0, 2);
            }

            // Format integer part with commas
            let formattedInteger = '';
            if (integerPart) {
                formattedInteger = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            let newValue = formattedInteger;
            if (hasDecimal) {
                newValue += '.' + decimalPart;
            }

            this.value = newValue;

            // Find new cursor position
            let newCursorPosition = 0;
            let nonCommaCount = 0;
            for (let i = 0; i < newValue.length; i++) {
                if (nonCommaCount === nonCommaCountBeforeCursor) {
                    newCursorPosition = i;
                    break;
                }
                if (newValue[i] !== ',') {
                    nonCommaCount++;
                }
                if (i === newValue.length - 1) {
                    newCursorPosition = newValue.length;
                }
            }

            let input = this;
            try {
                input.setSelectionRange(newCursorPosition, newCursorPosition);
                // Force cursor setting in a deferred execution to run after standard event loop cycles
                setTimeout(function () {
                    try {
                        input.setSelectionRange(newCursorPosition, newCursorPosition);
                    } catch (e) {}
                }, 0);
            } catch (e) {
                // Ignore if not supported
            }
        });

        // Prevent typing non-decimal characters directly
        $(document).on('keypress', '.decimal-only', function (e) {
            let charCode = (e.which) ? e.which : e.keyCode;

            // Allow navigation and control keys (backspace, delete, arrows, etc.)
            if (charCode < 32 || e.ctrlKey || e.metaKey) {
                return;
            }

            // 46 is dot, 48-57 are numbers
            if (charCode !== 46 && (charCode < 48 || charCode > 57)) {
                e.preventDefault();
            }
            // Prevent multiple dots
            if (charCode === 46 && $(this).val().includes('.')) {
                e.preventDefault();
            }
        });


        document.addEventListener('DOMContentLoaded', function () {

            document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(tab => {
                tab.addEventListener('shown.bs.tab', function (e) {
                    const hash = e.target.getAttribute('href');
                    history.replaceState(null, null, hash);
                });
            });

            const hash = window.location.hash;

            if (hash) {
                const tab = document.querySelector(`a[href="${hash}"]`);
                if (tab) {
                    new bootstrap.Tab(tab).show();
                }
            }

        });

        function numberFormat(e, showDecimals = true) {
            let raw = e.value.replace(/,/g, '');

            let num = Number(raw);

            if (isNaN(num)) {
                e.value = '';
                return;
            }

            let amount = num.toLocaleString('en-PH', {
                minimumFractionDigits: showDecimals ? 2 : 0,
                maximumFractionDigits: showDecimals ? 2 : 0
            });

            e.value = amount;
        }

        $(document).on("focus", ".decimal-only", function () {
            if ($(this).prop('readonly')) return;

            let val = this.value.trim();
            if (val) {
                let clean = val.replace(/,/g, '');
                let parsed = parseFloat(clean);
                if (parsed === 0) {
                    this.value = '';
                }
            }
        });

        $(document).on("blur", ".decimal-only", function () {
            numberFormat(this, 2);
        });

        function updateBranch(branchId) {
            $.ajax({
                url: "{{ route('user.update-branch') }}",
                type: "POST",
                data: {
                    branch_id: branchId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }


    </script>


    @stack('js')
</body>

</html>