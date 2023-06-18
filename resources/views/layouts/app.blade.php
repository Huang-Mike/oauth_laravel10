<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

    <!-- DataTable -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css" />
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
</head>

<style>
    .gear-icon {
        width: 5em;
        height: 5em;
        cursor: pointer;

    }

    .gear-icon:hover {
        border-radius: 3em;
        background-color: gainsboro;
    }
</style>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <!-- Left collapsing bar control button. -->
            @auth
                <div class="gear-icon d-flex justify-content-center align-items-center" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasWithBackdrop" aria-controls="offcanvasWithBackdrop">
                    <img style="width: 50%;" src="{{ asset('/img/gear-icon.png') }}">
                </div>
            @endauth
            <div class="container">
                <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('admin.login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.login.view') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.register.view') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('admin.logout') }}"
                                        onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST"
                                        class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        @auth
            <!-- Collapse column on the left. -->
            <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasWithBackdrop"
                aria-labelledby="offcanvasWithBackdropLabel">
                <div class="offcanvas-header" style="justify-content: flex-end;">
                    {{-- <h5 class="offcanvas-title" id="offcanvasWithBackdropLabel">Offcanvas with backdrop</h5> --}}
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><a style="color: black" href="{{ route('clients.list') }}">Clients</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="create-form" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modal-title">Client Name or Create</h5>
                        </div>
                        <div id="modal-content" class="modal-body text-center">HTML</div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button id='modal-confirm' type="button" class="btn btn-primary" data-bs-dismiss="modal">Update
                                or create</button>
                        </div>
                    </div>
                </div>
            </div>


            <script>
                // $('#create-form').on('show.bs.modal', function(event) {
                //     var button = event.relatedTarget,
                //         type = $(button).data('type'),
                //         html = $(button).data('html'),
                //         modalTitle = $(this).find('.modal-title'),
                //         modalConfirm = $(this).find('#modal-confirm'),
                //         modalContent = $(this).find('#modal-content');
                //     modalTitle.text(type);
                //     modalConfirm.text(type);
                // });
            </script>

        @endauth

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>

</html>
