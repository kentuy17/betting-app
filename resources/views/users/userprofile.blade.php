@extends('layouts.app')
@section('additional-styles')
  <style>
    /*! CSS Used from: https://argon-dashboard-pro-laravel.creative-tim.com/assets/css/argon-dashboard.css */
    *,
    *::before,
    *::after {
      box-sizing: border-box;
    }

    h5 {
      margin-top: 0;
      margin-bottom: .5rem;
      font-weight: 400;
      line-height: 1.2;
      color: #344767;
    }

    h5 {
      font-size: 1.25rem;
    }

    p {
      margin-top: 0;
      margin-bottom: 1rem;
    }

    label {
      display: inline-block;
    }

    button {
      border-radius: 0;
    }

    button:focus:not(:focus-visible) {
      outline: 0;
    }

    input,
    button,
    select {
      margin: 0;
      font-family: inherit;
      font-size: inherit;
      line-height: inherit;
    }

    button,
    select {
      text-transform: none;
    }

    select {
      word-wrap: normal;
    }

    select:disabled {
      opacity: 1;
    }

    button,
    [type=submit] {
      -webkit-appearance: button;
    }

    button:not(:disabled),
    [type=submit]:not(:disabled) {
      cursor: pointer;
    }

    [hidden] {
      display: none !important;
    }

    .row {
      --bs-gutter-x: 1.5rem;
      --bs-gutter-y: 0;
      display: flex;
      flex-wrap: wrap;
      margin-top: calc(-1 * var(--bs-gutter-y));
      margin-right: calc(-0.5 * var(--bs-gutter-x));
      margin-left: calc(-0.5 * var(--bs-gutter-x));
    }

    .col-3 {
      flex: 0 0 auto;
      width: 25%;
    }

    .col-4 {
      flex: 0 0 auto;
      width: 33.33333333%;
    }

    .col-5 {
      flex: 0 0 auto;
      width: 41.66666667%;
    }

    .col-6 {
      flex: 0 0 auto;
      width: 50%;
    }

    @media (min-width:576px) {
      .col-sm-3 {
        flex: 0 0 auto;
        width: 25%;
      }

      .col-sm-4 {
        flex: 0 0 auto;
        width: 33.33333333%;
      }

      .col-sm-5 {
        flex: 0 0 auto;
        width: 41.66666667%;
      }

      .col-sm-8 {
        flex: 0 0 auto;
        width: 66.66666667%;
      }
    }

    @media (min-width:768px) {
      .col-md-6 {
        flex: 0 0 auto;
        width: 50%;
      }
    }

    .form-label {
      margin-bottom: .5rem;
      font-size: .75rem;
      font-weight: 700;
      color: #344767;
    }

    .form-control {
      display: block;
      width: 100%;
      padding: .5rem .75rem;
      font-size: .875rem;
      font-weight: 400;
      line-height: 1.4rem;
      color: #495057;
      background-color: #fff;
      background-clip: padding-box;
      border: 1px solid #d2d6da;
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
      border-radius: .5rem;
      transition: box-shadow .15s ease, border-color .15s ease;
    }

    @media (prefers-reduced-motion:reduce) {
      .form-control {
        transition: none;
      }
    }

    .form-control:focus {
      color: #495057;
      background-color: #fff;
      border-color: #fb6340;
      outline: 0;
      box-shadow: 0 3px 9px transparent, 3px 4px 8px rgba(94, 114, 228, .1);
    }

    .form-control::placeholder {
      color: #adb5bd;
      opacity: 1;
    }

    .form-control:disabled {
      background-color: #e9ecef;
      opacity: 1;
    }

    .input-group {
      position: relative;
      display: flex;
      flex-wrap: wrap;
      align-items: stretch;
      width: 100%;
    }

    .input-group>.form-control {
      position: relative;
      flex: 1 1 auto;
      width: 1%;
      min-width: 0;
    }

    .input-group>.form-control:focus {
      z-index: 3;
    }

    .btn {
      display: inline-block;
      font-weight: 700;
      line-height: 1.5;
      color: #67748e;
      text-align: center;
      vertical-align: middle;
      cursor: pointer;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
      background-color: transparent;
      border: 1px solid transparent;
      padding: .625rem 1.25rem;
      font-size: .875rem;
      border-radius: .5rem;
      transition: all .15s ease-in;
    }

    @media (prefers-reduced-motion:reduce) {
      .btn {
        transition: none;
      }
    }

    .btn:hover {
      color: #67748e;
    }

    .btn:focus {
      outline: 0;
      box-shadow: 0 7px 14px rgba(50, 50, 93, .1), 0 3px 6px rgba(0, 0, 0, .08);
    }

    .btn:disabled {
      pointer-events: none;
      opacity: .65;
    }

    .btn-sm {
      padding: .5rem 2rem;
      font-size: .75rem;
      border-radius: .5rem;
    }

    .card {
      position: relative;
      display: flex;
      flex-direction: column;
      min-width: 0;
      word-wrap: break-word;
      background-color: #fff;
      background-clip: border-box;
      border: 0 solid rgba(0, 0, 0, .125);
      border-radius: 1rem;
    }

    .card-body {
      flex: 1 1 auto;
      padding: 1rem;
    }

    .card-header {
      padding: .5rem 1rem;
      margin-bottom: 0;
      background-color: #fff;
      border-bottom: 0 solid rgba(0, 0, 0, .125);
    }

    .card-header:first-child {
      border-radius: 1rem 1rem 0 0;
    }

    .float-end {
      float: right !important;
    }

    .d-none {
      display: none !important;
    }

    .align-self-center {
      align-self: center !important;
    }

    .mt-4 {
      margin-top: 1.5rem !important;
    }

    .mt-6 {
      margin-top: 4rem !important;
    }

    .mb-0 {
      margin-bottom: 0 !important;
    }

    .pt-0 {
      padding-top: 0 !important;
    }

    .pt-1 {
      padding-top: .25rem !important;
    }

    .btn {
      margin-bottom: 1rem;
      letter-spacing: -.025rem;
      text-transform: none;
      box-shadow: 0 4px 6px rgba(50, 50, 93, .1), 0 1px 3px rgba(0, 0, 0, .08);
    }

    .btn:not([class*=btn-outline-]) {
      border: 0;
    }

    .btn:active,
    .btn:active:focus,
    .btn:active:hover {
      box-shadow: 0 7px 14px rgba(50, 50, 93, .1), 0 3px 6px rgba(0, 0, 0, .08);
      transform: translateY(-1px);
    }

    .btn:hover:not(.btn-icon-only) {
      box-shadow: 0 7px 14px rgba(50, 50, 93, .1), 0 3px 6px rgba(0, 0, 0, .08);
      transform: translateY(-1px);
    }

    .btn.bg-gradient-dark:hover {
      background-color: #344767;
      border-color: #344767;
    }

    .btn.bg-gradient-dark:not(:disabled):not(.disabled):active {
      color: color-yiq(#344767);
      background-color: #344767;
    }

    .btn.bg-gradient-dark:focus {
      color: #fff;
    }

    .btn.bg-gradient-dark {
      color: #fff;
    }

    .btn.bg-gradient-dark:hover {
      color: #fff;
    }

    .card {
      box-shadow: 0 0 2rem 0 rgba(136, 152, 170, .15);
    }

    .card .card-header {
      padding: 1.5rem;
    }

    .card .card-body {
      font-family: open sans, sans-serif;
      padding: 1.5rem;
    }

    .input-group {
      border-radius: .5rem;
    }

    .input-group {
      transition: box-shadow .15s ease, border-color .15s ease;
    }

    .input-group .form-control:focus {
      border-left: 1px solid #fb6340 !important;
      border-right: 1px solid #fb6340 !important;
    }

    label,
    .form-label {
      font-size: .75rem;
      font-weight: 700;
      margin-bottom: .5rem;
      color: #344767;
      margin-left: .25rem;
    }

    .bg-gradient-dark {
      background-image: linear-gradient(310deg, #212229 0%, #212529 100%);
    }

    html * {
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    h5 {
      font-size: 1.25rem;
      line-height: 1.375;
    }

    @media (max-width:575.98px) {
      h5 {
        font-size: 1.25rem;
      }
    }

    p {
      font-size: 1rem;
      font-weight: 400;
      line-height: 1.6;
    }

    h5 {
      font-weight: 600;
    }

    .text-xs {
      line-height: 1.25;
    }

    p {
      font-size: 1rem;
    }

    .text-xs {
      font-size: .75rem !important;
    }

    p {
      line-height: 1.625;
      font-weight: 400;
    }

    .choices {
      position: relative;
      margin-bottom: 24px;
      font-size: 16px;
    }

    .choices:focus {
      outline: none;
    }

    .choices:last-child {
      margin-bottom: 0;
    }

    .choices [hidden] {
      display: none !important;
    }

    .choices[data-type*=select-one] {
      cursor: pointer;
    }

    .choices[data-type*=select-one] .choices__inner {
      padding-bottom: 7.5px;
    }

    .choices[data-type*=select-one] .choices__input {
      display: block;
      width: 100%;
      padding: 10px;
      border-bottom: 1px solid #ddd;
      background-color: #fff;
      margin: 0;
    }

    .choices[data-type*=select-one]:after {
      content: "\f107";
      font: normal normal normal 14px/1 FontAwesome;
      position: absolute;
      right: 13.5px;
      top: 14px;
      pointer-events: none;
      transition: .3s ease;
    }

    .choices__inner {
      display: flex;
      align-items: center;
      vertical-align: top;
      width: 100%;
      background-color: #fff;
      padding: .5rem .75rem;
      border: 1px solid #d2d6da;
      border-radius: .5rem;
      font-size: 14px;
      min-height: 40px;
      overflow: hidden;
    }

    .choices__list {
      margin: 0;
      padding-left: 0;
      list-style: none;
    }

    .choices__list--single {
      display: inline-block;
      width: 100%;
    }

    .choices__list--single .choices__item {
      width: 100%;
    }

    .choices__list--dropdown {
      visibility: hidden;
      z-index: 1;
      position: absolute;
      width: 100%;
      background-color: #fff;
      border: 1px solid #ddd;
      top: 100%;
      margin-top: -1px;
      border-bottom-left-radius: 2.5px;
      border-bottom-right-radius: 2.5px;
      word-break: break-all;
      will-change: visibility;
    }

    .choices__list--dropdown .choices__list {
      position: relative;
      max-height: 300px;
      overflow: auto;
      -webkit-overflow-scrolling: touch;
      will-change: scroll-position;
    }

    .choices__list--dropdown .choices__item {
      position: relative;
      padding: .3rem 1rem;
      font-size: .875rem;
      border-radius: .5rem;
      transition: background-color .3s ease, color .3s ease;
    }

    @media (min-width:640px) {
      .choices__list--dropdown .choices__item--selectable {
        padding-right: 100px;
      }

      .choices__list--dropdown .choices__item--selectable:after {
        content: attr(data-select-text);
        font-size: 12px;
        opacity: 0;
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        transition: opacity .3s ease;
      }
    }

    .choices__list--dropdown .choices__item--selectable.is-highlighted {
      background-color: #f2f2f2;
    }

    .choices__list--dropdown .choices__item--selectable.is-highlighted:after {
      opacity: .5;
    }

    .choices__item {
      cursor: default;
    }

    .choices__item--selectable {
      cursor: pointer;
    }

    .choices__input {
      display: inline-block;
      vertical-align: baseline;
      background-color: #fff;
      font-size: 14px;
      margin-bottom: 5px;
      border: 0;
      border-radius: 0;
      max-width: 100%;
      padding: 4px 0 4px 2px;
    }

    .choices__input:focus {
      outline: 0;
    }

    .choices__placeholder {
      opacity: .5;
    }

    .choices__list--dropdown {
      display: block;
      opacity: 0;
      top: 19px;
      transform-origin: 50% 0;
      pointer-events: none;
      -webkit-backface-visibility: hidden;
      backface-visibility: hidden;
      will-change: transform, box-shadow;
      border: none;
      border-radius: .5rem;
      box-shadow: 0 8px 26px -4px rgba(20, 20, 20, .15), 0 8px 9px -5px rgba(20, 20, 20, .06);
      transform: perspective(999px) rotateX(-10deg) translateZ(0) translate3d(0px, 37px, 0px) !important;
      padding: 1rem .5rem;
      transition: visibility .25s, opacity .25s, transform .25s;
    }

    .choices__list--dropdown:before {
      font-family: fontawesome;
      content: "\f0d8";
      position: absolute;
      top: 0;
      left: 28px;
      right: auto;
      font-size: 22px;
      color: #fff;
      transition: top .35s ease;
    }

    .choices[data-type*=select-one] .choices__input {
      border-bottom-color: #f8f9fa;
    }

    .choices__list--dropdown .choices__item--selectable.is-highlighted {
      background: #e9ecef;
      color: #344767;
    }

    .choices {
      position: relative;
      margin-bottom: 24px;
      font-size: 16px;
    }

    .choices:focus {
      outline: none;
    }

    .choices:last-child {
      margin-bottom: 0;
    }

    .choices [hidden] {
      display: none !important;
    }

    .choices[data-type*=select-one] {
      cursor: pointer;
    }

    .choices[data-type*=select-one] .choices__inner {
      padding-bottom: 7.5px;
    }

    .choices[data-type*=select-one] .choices__input {
      display: block;
      width: 100%;
      padding: 10px;
      border-bottom: 1px solid #ddd;
      background-color: #fff;
      margin: 0;
    }

    .choices[data-type*=select-one]:after {
      content: "\f107";
      font: normal normal normal 14px/1 FontAwesome;
      position: absolute;
      right: 13.5px;
      top: 14px;
      pointer-events: none;
      transition: .3s ease;
    }

    .choices__inner {
      display: flex;
      align-items: center;
      vertical-align: top;
      width: 100%;
      background-color: #fff;
      padding: .5rem .75rem;
      border: 1px solid #d2d6da;
      border-radius: .5rem;
      font-size: 14px;
      min-height: 40px;
      overflow: hidden;
    }

    .choices__list {
      margin: 0;
      padding-left: 0;
      list-style: none;
    }

    .choices__list--single {
      display: inline-block;
      width: 100%;
    }

    .choices__list--single .choices__item {
      width: 100%;
    }

    .choices__list--dropdown {
      visibility: hidden;
      z-index: 1;
      position: absolute;
      width: 100%;
      background-color: #fff;
      border: 1px solid #ddd;
      top: 100%;
      margin-top: -1px;
      border-bottom-left-radius: 2.5px;
      border-bottom-right-radius: 2.5px;
      word-break: break-all;
      will-change: visibility;
    }

    .choices__list--dropdown .choices__list {
      position: relative;
      max-height: 300px;
      overflow: auto;
      -webkit-overflow-scrolling: touch;
      will-change: scroll-position;
    }

    .choices__list--dropdown .choices__item {
      position: relative;
      padding: .3rem 1rem;
      font-size: .875rem;
      border-radius: .5rem;
      transition: background-color .3s ease, color .3s ease;
    }

    @media (min-width:640px) {
      .choices__list--dropdown .choices__item--selectable {
        padding-right: 100px;
      }

      .choices__list--dropdown .choices__item--selectable:after {
        content: attr(data-select-text);
        font-size: 12px;
        opacity: 0;
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        transition: opacity .3s ease;
      }
    }

    .choices__list--dropdown .choices__item--selectable.is-highlighted {
      background-color: #f2f2f2;
    }

    .choices__list--dropdown .choices__item--selectable.is-highlighted:after {
      opacity: .5;
    }

    .choices__item {
      cursor: default;
    }

    .choices__item--selectable {
      cursor: pointer;
    }

    .choices__input {
      display: inline-block;
      vertical-align: baseline;
      background-color: #fff;
      font-size: 14px;
      margin-bottom: 5px;
      border: 0;
      border-radius: 0;
      max-width: 100%;
      padding: 4px 0 4px 2px;
    }

    .choices__input:focus {
      outline: 0;
    }

    .choices__placeholder {
      opacity: .5;
    }

    .choices__list--dropdown {
      display: block;
      opacity: 0;
      top: 19px;
      transform-origin: 50% 0;
      pointer-events: none;
      -webkit-backface-visibility: hidden;
      backface-visibility: hidden;
      will-change: transform, box-shadow;
      border: none;
      border-radius: .5rem;
      box-shadow: 0 8px 26px -4px rgba(20, 20, 20, .15), 0 8px 9px -5px rgba(20, 20, 20, .06);
      transform: perspective(999px) rotateX(-10deg) translateZ(0) translate3d(0px, 37px, 0px) !important;
      padding: 1rem .5rem;
      transition: visibility .25s, opacity .25s, transform .25s;
    }

    .choices__list--dropdown:before {
      font-family: fontawesome;
      content: "\f0d8";
      position: absolute;
      top: 0;
      left: 28px;
      right: auto;
      font-size: 22px;
      color: #fff;
      transition: top .35s ease;
    }

    .choices[data-type*=select-one] .choices__input {
      border-bottom-color: #f8f9fa;
    }

    .choices__list--dropdown .choices__item--selectable.is-highlighted {
      background: #e9ecef;
      color: #344767;
    }

    @keyframes rotate {
      0% {
        -webkit-transform: rotate(0deg);
        -moz-transform: rotate(0deg);
      }

      100% {
        -webkit-transform: rotate(360deg);
        -moz-transform: rotate(360deg);
      }
    }

    .btn-flat {
      border-radius: 0;
      border-top-left-radius: 0px;
      border-bottom-left-radius: 0px;
      border-width: 1px;
    }
  </style>
@endsection
@section('content')
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12">
        @if (Auth::user()->share_holder || Auth::user()->agent)
          <div class="card card-body mb-0 mt-3" id="profile">
            <div class="row justify-content-center">
              <div class="col-sm-auto col-4">
                <div class="avatar avatar-xl position-relative">
                  <div>
                    <label for="file-input" class="btn btn-sm btn-icon-only bg-gradient-light position-absolute bottom-0 end-0 mb-n2 me-n2">
                      <i class="fa fa-pen top-0" data-bs-toggle="tooltip" data-bs-placement="top" title="" aria-hidden="true" data-bs-original-title="Edit Image" aria-label="Edit Image"></i>
                      <span class="sr-only">Edit Image</span>
                    </label>
                    <span class="h-12 w-12 rounded-full overflow-hidden bg-gray-100">
                      <img src="{{ asset('img/aficionado-logo.png') }}" alt="bruce" class="w-100 border-radius-lg shadow-sm">
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-sm-auto col-8 my-auto">
                <div class="h-100">
                  <h5 class="mb-1 font-weight-bolder">{{ Auth::user()->username }}</h5>
                  <p class="mb-0 font-weight-bold text-sm font-bold">
                    @if (Auth::user()->share_holder)
                      BOSS / {{ Auth::user()->share_holder->role_description }}
                    @elseif(Auth::user()->agent)
                      @if (Auth::user()->id == 10)
                        MASTER
                      @endif COMMISSION <span class="text-black">
                        @if (Auth::user()->agent->type == 'sub-agent')
                          {{ Auth::user()->agent->percent }}%
                        @else
                          6%
                        @endif
                      </span>
                    @else
                      {{ Auth::user()->user_role->name }}
                    @endif
                  </p>
                </div>
              </div>
              <div class="col-sm-auto ms-sm-auto mt-sm-0 mt-3 d-flex">
                <label class="form-check-label mb-0 col-6 text-right" style="line-height: 3;">
                  <span id="profileVisibility" class="commission-label mr-2">₱
                    @if (Auth::user()->share_holder)
                      {{ number_format(Auth::user()->share_holder->current_commission, 2) }}
                    @else
                      {{ number_format(Auth::user()->agent->current_commission, 2) }}
                    @endif

                  </span>
                  @if (Auth::user()->agent)
                  @endif
                </label>
                <div class="form-check pl-0 mr-5">
                  @if (Auth::user()->share_holder)
                    <a class="py-1 btn bg-gradient-dark btn-sm float-start" id="convert-com-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="" aria-hidden="true" data-bs-original-title="Convert Into Points" aria-label="Convert Into Points">
                      <svg width="28px" height="28px" id="svg-convert" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" stroke-width="3" stroke="#ffffff" fill="none">
                        <path d="M55.47,31.14A23.51,23.51,0,0,1,12.69,45.6" stroke-linecap="round" />
                        <path d="M8.46,32.74a24,24,0,0,1,.42-5,23.51,23.51,0,0,1,42.29-9.14" stroke-linecap="round" />
                        <polyline points="40.6 17.6 51.45 18.87 52.53 8.69" stroke-linecap="round" />
                        <polyline points="23.05 46.33 12.21 45.06 11.12 55.24" stroke-linecap="round" />
                        <path d="M39,25.57a7.09,7.09,0,0,0-6.65-4.29c-6,0-6.21,4.29-6.21,4.29s-.9,5.28,6.43,5.85C40.18,32,39,37.26,39,37.26s-.78,4.58-6.43,4.87-7.41-5.65-7.41-5.65" />
                        <line x1="32.33" y1="17.48" x2="32.33" y2="46.52" />
                      </svg>
                    </a>
                  @else
                    <a class="py-1 btn bg-gradient-dark btn-sm float-start" id="convert-agent-comm" data-bs-toggle="tooltip" data-bs-placement="top" title="" aria-hidden="true" data-bs-original-title="Convert Into Points" aria-label="Convert Into Points">
                      <svg width="28px" height="28px" id="svg-convert" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" stroke-width="3" stroke="#ffffff" fill="none">
                        <path d="M55.47,31.14A23.51,23.51,0,0,1,12.69,45.6" stroke-linecap="round" />
                        <path d="M8.46,32.74a24,24,0,0,1,.42-5,23.51,23.51,0,0,1,42.29-9.14" stroke-linecap="round" />
                        <polyline points="40.6 17.6 51.45 18.87 52.53 8.69" stroke-linecap="round" />
                        <polyline points="23.05 46.33 12.21 45.06 11.12 55.24" stroke-linecap="round" />
                        <path d="M39,25.57a7.09,7.09,0,0,0-6.65-4.29c-6,0-6.21,4.29-6.21,4.29s-.9,5.28,6.43,5.85C40.18,32,39,37.26,39,37.26s-.78,4.58-6.43,4.87-7.41-5.65-7.41-5.65" />
                        <line x1="32.33" y1="17.48" x2="32.33" y2="46.52" />
                      </svg>
                    </a>
                  @endif

                </div>
              </div>
            </div>
          </div>
        @endif
        <div class="card col-md-12 mt-3">
          <div class="card-body">
            <form method="POST" action="{{ url('/user/profile/') }}">
              <div class="col-md-8 card" id="basic-info">
                <div class="card-header">
                  <h5>Profile Info</h5>
                  @include('layouts.flash-message')
                </div>
                <div class="card-body pt-0">
                  @csrf
                  @if (Auth::user()->user_role->name != 'Player')
                    <div class="row mb-4">
                      <div class="col-12">
                        <label class="text-black form-label">User Role</label>
                        <div class="input-group">
                          <input id="user_role" class="form-control disabled" title="User Role" type="text" disabled="" value="{{ Auth::user()->user_role->name }}">
                        </div>
                      </div>
                    </div>
                  @endif
                  <div class="row">
                    <div class="col-12">
                      <label class="text-black form-label">Points</label>
                      <div class="input-group">
                        <input id="credit_points" class="form-control disabled" type="text" disabled="" value="{{ number_format($user->points, 2, '.', ',') }}">
                      </div>
                    </div>
                  </div>
                  @if (Auth::user()->agent)
                    <div class="row mt-4">
                      <div class="col-12">
                        <label for="referral-link" class="text-black form-label">Referral Link</label>
                        <div class="input-group">
                          <?php
                          if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                              $http = 'https://';
                          } else {
                              $http = 'http://';
                          }
                          ?>
                          <input id="referral-link" class="form-control disabled text-xs italic" type="text" disabled="" value="{{ $http . $_SERVER['HTTP_HOST'] . '/register?rid=' . Auth::user()->agent->rid }}">
                          <span class="input-group-append">
                            <button id="copy-icon" data-bs-toggle="tooltip" title="Copied!" data-bs-trigger="click" type="button" style="margin-bottom:0;" class="px-2 py-2.5 btn btn-primary btn-flat">
                              <i class="fa-solid fa-copy"></i>
                            </button>
                          </span>
                        </div>
                      </div>
                    </div>
                  @endif
                  <div class="row">
                    <div class="col-12">
                      <label class="text-black form-label mt-4">Username</label>
                      <div class="input-group">
                        <input id="username" name="username" class="form-control disabled" disabled type="text" value="{{ $user->username }}" placeholder="Username">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12">
                      <label class="text-black form-label mt-4">Phone No.</label>
                      <div class="input-group">
                        <input id="phone_no" name="phone_no" class="form-control" type="text" value="{{ $user->phone_no }}" placeholder="09*********" {{ !Auth::user()->agent ? 'readonly' : '' }}>
                      </div>
                      @if (session('error'))
                        <p class="text-xs pt-1 text-danger">{{ session('error') }}</p>
                      @endif
                      @error('phone_no')
                        <p class="text-xs pt-1 text-danger">{{ $message }}</p>
                      @enderror
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <label class="text-black form-label mt-4">New Password</label>
                      <div class="input-group">
                        <input id="new_pass" name="new_pass" class="form-control" type="password" placeholder="******" autocomplete="new-password">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <label class="text-black form-label mt-4">Confirm Password</label>
                      <div class="input-group">
                        <input id="confirm_pass" name="confirm_pass" class="form-control" type="password" placeholder="******">
                      </div>
                    </div>
                  </div>
                  {{ session('error') }}
                  <button type="submit" class="btn bg-gradient-dark btn-sm float-end mt-4">Update</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('additional-scripts')
  <script>
    $(function() {
      $('button[type="submit"]').on('click', function(e) {
        var pass = $('#new_pass').val();
        var confirm = $('#confirm_pass').val();

        if (pass !== confirm) {
          alert("Password Mismatch!")
          return;
        }
      });

      $('[data-bs-toggle="tooltip"]').tooltip()

      $('#convert-com-btn').on('click', async function(e) {
        e.preventDefault();

        let pointsToConvert = prompt('Enter points to convert:', 0);
        let convert = await axios.post('/commission/convert', {
          points: pointsToConvert
        })

        console.log(convert);

        $('#svg-convert').css({
          'animation-name': 'rotate',
          'animation-duration': '1s',
          'animation-iteration-count': 3,
        });

        setTimeout(() => {
          $('#svg-convert').removeAttr('style');
          $('#profileVisibility').text(convert.data.current_commission);
          $('#credit_points').val(convert.data.points)
        }, 3000);


      });

      $('#convert-agent-comm').on('click', async function(e) {
        e.preventDefault();

        try {
          let pointsToConvert = prompt('Enter points to convert:', 0);
          let convert = await axios.post('/agent/commission-convert', {
            points: pointsToConvert
          })

          $('#svg-convert').css({
            'animation-name': 'rotate',
            'animation-duration': '1s',
            'animation-iteration-count': 3,
          });

          setTimeout(() => {
            $('#svg-convert').removeAttr('style');
            $('#profileVisibility').text(`₱ ${convert.data.data.current_commission}`);
            $('#credit_points').val(convert.data.data.points)
            alert('Successfully converted points')
          }, 3000);
        } catch (error) {
          // console.log(error.response.data.message);
          alert(error.response.data.message);
        }
      });

      $('#copy-icon').on('click', function(e) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($('#referral-link').val()).select();
        document.execCommand("copy");
        $temp.remove();
        setTimeout(() => {
          $(this).tooltip('hide')
        }, 3000);
      })

    })
  </script>
  <script src="{{ asset('js/userprofile.js') }}" defer></script>
@endsection
