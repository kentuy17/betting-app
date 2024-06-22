<!DOCTYPE html>
<html lang="en" data-color-mode="auto" data-light-theme="light" data-dark-theme="dark" data-a11y-animated-images="system" data-turbo-loaded="">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" href="{{ asset('img/sabong-aficionado.ico') }}" type="image/x-icon">
  <title>{{ config('app.name', 'Sabong Aficionado') }}</title>

  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-L6BFWJNTWB"></script>
  <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', 'G-L6BFWJNTWB');
  </script>

  <!-- Fonts -->
  <link rel="dns-prefetch" href="//fonts.gstatic.com">
  <link rel="stylesheet" href="{{ asset('css/github-auth.css') }}" />
</head>

<body class="logged-out env-production page-responsive session-authentication" style="word-wrap: break-word;">
  <div data-turbo-body="" class="logged-out env-production page-responsive session-authentication" style="word-wrap: break-word;">
    <div class="position-relative js-header-wrapper ">
      <a href="#start-of-content" class="px-2 py-4 color-bg-accent-emphasis color-fg-on-emphasis show-on-focus js-skip-to-content">Skip to content</a>
      <span data-view-component="true" class="progress-pjax-loader Progress position-fixed width-full">
        <span style="width: 0%;" data-view-component="true" class="Progress-item progress-pjax-loader-bar left-0 top-0 color-bg-accent-emphasis"></span>
      </span>
      <div class="header header-logged-out width-full pt-5 pb-2" role="banner">
        <div class="container clearfix width-full text-center">
          <a class="header-logo" href="/" aria-label="Homepage">
            <img src="{{ asset('img/aficionado-logo-crop.png') }}" width="120" alt="Sabong Aficionado">
          </a>
        </div>
      </div>
    </div>
    <div id="start-of-content" class="show-on-focus"></div>
    <div class="application-main " data-commit-hovercards-enabled="" data-discussion-hovercards-enabled="" data-issue-and-pr-hovercards-enabled="">
      <main>
        <div class="auth-form" id="login">
          @if ($errors->any())
            <div id="js-flash-container" data-turbo-replace="">
              <div class="flash flash-full flash-error  ">
                <div class="px-2">
                  <button autofocus="" class="flash-close js-flash-close" type="button" aria-label="Dismiss this message">
                    <svg aria-hidden="true" height="16" viewBox="0 0 16 16" version="1.1" width="16" data-view-component="true" class="octicon octicon-x">
                      <path d="M3.72 3.72a.75.75 0 0 1 1.06 0L8 6.94l3.22-3.22a.749.749 0 0 1 1.275.326.749.749 0 0 1-.215.734L9.06 8l3.22 3.22a.749.749 0 0 1-.326 1.275.749.749 0 0 1-.734-.215L8 9.06l-3.22 3.22a.751.751 0 0 1-1.042-.018.751.751 0 0 1-.018-1.042L6.94 8 3.72 4.78a.75.75 0 0 1 0-1.06Z"></path>
                    </svg>
                  </button>
                  <div aria-atomic="true" role="alert" class="js-flash-alert">
                    {!! implode('', $errors->all('<div>:message</div>')) !!}</div>
                </div>
              </div>
            </div>
          @endif
          {{-- <div class="auth-form-body mt-3">
            <form action="{{ route('login') }}" accept-charset="UTF-8" method="post">
              @csrf
              <label for="username">Username</label>
              <input type="text" name="username" id="username" class="form-control input-block js-login-field" autocapitalize="none" autocorrect="off" autocomplete="username" autofocus="autofocus">
              <div class="position-relative">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control form-control input-block js-password-field" autocomplete="current-password">
                <button type="submit" class="btn btn-primary btn-block js-sign-in-button">Login</button>
                <a class="label-link position-absolute top-0 right-0" tabindex="0" href="/password_reset">Forgot password?</a>
              </div>
            </form>
          </div> --}}
          {{-- <p class="login-callout mt-3">New Player? <a href="/register">Create an account</a>.</p> --}}
          <div class="login-callout mt-3">
            <h2>SITE MAINTENANCE</h1>
          </div>
        </div>
      </main>
    </div>

    <div class="footer container-lg p-responsive py-6 mt-6 f6" role="contentinfo">
      <ul class="list-style-none d-flex flex-justify-center">
        <li class="mr-3">
          <a href="/site/terms">Terms</a>
        </li>
        <li class="mr-3">
          <a href="/site/privacy">Privacy</a>
        </li>
        <li class="mr-3">
          <a href="https://media.philstar.com/photos/2021/11/07/ss2021-03-3022-42-09_2021-11-07_21-59-48.jpg">Security</a>
        </li>
        <li>
          <a class="Link--secondary" href="#" target="_blank">Contact Admin</a>
        </li>
      </ul>
    </div>
    <div id="ajax-error-message" class="ajax-error-message flash flash-error" hidden="">
      <svg aria-hidden="true" height="16" viewBox="0 0 16 16" version="1.1" width="16" data-view-component="true" class="octicon octicon-alert">
        <path d="M6.457 1.047c.659-1.234 2.427-1.234 3.086 0l6.082 11.378A1.75 1.75 0 0 1 14.082 15H1.918a1.75 1.75 0 0 1-1.543-2.575Zm1.763.707a.25.25 0 0 0-.44 0L1.698 13.132a.25.25 0 0 0 .22.368h12.164a.25.25 0 0 0 .22-.368Zm.53 3.996v2.5a.75.75 0 0 1-1.5 0v-2.5a.75.75 0 0 1 1.5 0ZM9 11a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z">
        </path>
      </svg>
      <button type="button" class="flash-close js-ajax-error-dismiss" aria-label="Dismiss error">
        <svg aria-hidden="true" height="16" viewBox="0 0 16 16" version="1.1" width="16" data-view-component="true" class="octicon octicon-x">
          <path d="M3.72 3.72a.75.75 0 0 1 1.06 0L8 6.94l3.22-3.22a.749.749 0 0 1 1.275.326.749.749 0 0 1-.215.734L9.06 8l3.22 3.22a.749.749 0 0 1-.326 1.275.749.749 0 0 1-.734-.215L8 9.06l-3.22 3.22a.751.751 0 0 1-1.042-.018.751.751 0 0 1-.018-1.042L6.94 8 3.72 4.78a.75.75 0 0 1 0-1.06Z">
          </path>
        </svg>
      </button>
      You can’t perform that action at this time.
    </div>

    <div class="js-stale-session-flash flash flash-warn flash-banner" hidden="">
      <svg aria-hidden="true" height="16" viewBox="0 0 16 16" version="1.1" width="16" data-view-component="true" class="octicon octicon-alert">
        <path d="M6.457 1.047c.659-1.234 2.427-1.234 3.086 0l6.082 11.378A1.75 1.75 0 0 1 14.082 15H1.918a1.75 1.75 0 0 1-1.543-2.575Zm1.763.707a.25.25 0 0 0-.44 0L1.698 13.132a.25.25 0 0 0 .22.368h12.164a.25.25 0 0 0 .22-.368Zm.53 3.996v2.5a.75.75 0 0 1-1.5 0v-2.5a.75.75 0 0 1 1.5 0ZM9 11a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z">
        </path>
      </svg>
    </div>
    <template id="site-details-dialog">
      <details class="details-reset details-overlay details-overlay-dark lh-default color-fg-default hx_rsm" open="">
        <summary role="button" aria-label="Close dialog"></summary>
        <details-dialog class="Box Box--overlay d-flex flex-column anim-fade-in fast hx_rsm-dialog hx_rsm-modal">
          <button class="Box-btn-octicon m-0 btn-octicon position-absolute right-0 top-0" type="button" aria-label="Close dialog" data-close-dialog="">
            <svg aria-hidden="true" height="16" viewBox="0 0 16 16" version="1.1" width="16" data-view-component="true" class="octicon octicon-x">
              <path d="M3.72 3.72a.75.75 0 0 1 1.06 0L8 6.94l3.22-3.22a.749.749 0 0 1 1.275.326.749.749 0 0 1-.215.734L9.06 8l3.22 3.22a.749.749 0 0 1-.326 1.275.749.749 0 0 1-.734-.215L8 9.06l-3.22 3.22a.751.751 0 0 1-1.042-.018.751.751 0 0 1-.018-1.042L6.94 8 3.72 4.78a.75.75 0 0 1 0-1.06Z">
              </path>
            </svg>
          </button>
          <div class="octocat-spinner my-6 js-details-dialog-spinner"></div>
        </details-dialog>
      </details>
    </template>
    <div class="Popover js-hovercard-content position-absolute" style="display: none; outline: none;" tabindex="0">
      <div class="Popover-message Popover-message--bottom-left Popover-message--large Box color-shadow-large" style="width:360px;"></div>
    </div>
    <template id="snippet-clipboard-copy-button">
      <div class="zeroclipboard-container position-absolute right-0 top-0">
        <clipboard-copy aria-label="Copy" class="ClipboardButton btn js-clipboard-copy m-2 p-0 tooltipped-no-delay" data-copy-feedback="Copied!" data-tooltip-direction="w">
          <svg aria-hidden="true" height="16" viewBox="0 0 16 16" version="1.1" width="16" data-view-component="true" class="octicon octicon-copy js-clipboard-copy-icon m-2">
            <path d="M0 6.75C0 5.784.784 5 1.75 5h1.5a.75.75 0 0 1 0 1.5h-1.5a.25.25 0 0 0-.25.25v7.5c0 .138.112.25.25.25h7.5a.25.25 0 0 0 .25-.25v-1.5a.75.75 0 0 1 1.5 0v1.5A1.75 1.75 0 0 1 9.25 16h-7.5A1.75 1.75 0 0 1 0 14.25Z">
            </path>
            <path d="M5 1.75C5 .784 5.784 0 6.75 0h7.5C15.216 0 16 .784 16 1.75v7.5A1.75 1.75 0 0 1 14.25 11h-7.5A1.75 1.75 0 0 1 5 9.25Zm1.75-.25a.25.25 0 0 0-.25.25v7.5c0 .138.112.25.25.25h7.5a.25.25 0 0 0 .25-.25v-7.5a.25.25 0 0 0-.25-.25Z">
            </path>
          </svg>
          <svg aria-hidden="true" height="16" viewBox="0 0 16 16" version="1.1" width="16" data-view-component="true" class="octicon octicon-check js-clipboard-check-icon color-fg-success d-none m-2">
            <path d="M13.78 4.22a.75.75 0 0 1 0 1.06l-7.25 7.25a.75.75 0 0 1-1.06 0L2.22 9.28a.751.751 0 0 1 .018-1.042.751.751 0 0 1 1.042-.018L6 10.94l6.72-6.72a.75.75 0 0 1 1.06 0Z">
            </path>
          </svg>
        </clipboard-copy>
      </div>
    </template>
    <template id="snippet-clipboard-copy-button-unpositioned">
      <div class="zeroclipboard-container">
        <clipboard-copy aria-label="Copy" class="ClipboardButton btn btn-invisible js-clipboard-copy m-2 p-0 tooltipped-no-delay d-flex flex-justify-center flex-items-center" data-copy-feedback="Copied!" data-tooltip-direction="w">
          <svg aria-hidden="true" height="16" viewBox="0 0 16 16" version="1.1" width="16" data-view-component="true" class="octicon octicon-copy js-clipboard-copy-icon">
            <path d="M0 6.75C0 5.784.784 5 1.75 5h1.5a.75.75 0 0 1 0 1.5h-1.5a.25.25 0 0 0-.25.25v7.5c0 .138.112.25.25.25h7.5a.25.25 0 0 0 .25-.25v-1.5a.75.75 0 0 1 1.5 0v1.5A1.75 1.75 0 0 1 9.25 16h-7.5A1.75 1.75 0 0 1 0 14.25Z">
            </path>
            <path d="M5 1.75C5 .784 5.784 0 6.75 0h7.5C15.216 0 16 .784 16 1.75v7.5A1.75 1.75 0 0 1 14.25 11h-7.5A1.75 1.75 0 0 1 5 9.25Zm1.75-.25a.25.25 0 0 0-.25.25v7.5c0 .138.112.25.25.25h7.5a.25.25 0 0 0 .25-.25v-7.5a.25.25 0 0 0-.25-.25Z">
            </path>
          </svg>
          <svg aria-hidden="true" height="16" viewBox="0 0 16 16" version="1.1" width="16" data-view-component="true" class="octicon octicon-check js-clipboard-check-icon color-fg-success d-none">
            <path d="M13.78 4.22a.75.75 0 0 1 0 1.06l-7.25 7.25a.75.75 0 0 1-1.06 0L2.22 9.28a.751.751 0 0 1 .018-1.042.751.751 0 0 1 1.042-.018L6 10.94l6.72-6.72a.75.75 0 0 1 1.06 0Z">
            </path>
          </svg>
        </clipboard-copy>
      </div>
    </template>
  </div>
  <div id="js-global-screen-reader-notice" class="sr-only" aria-live="polite"></div>
</body>

</html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script>
  $(function() {
    $('.js-flash-close').on('click', function() {
      $('#js-flash-container').hide()
    })
  })
</script>
