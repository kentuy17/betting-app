@extends('layouts.app')
@section('additional-styles')
<style>
  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  .v-application a {
    cursor: pointer;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  *,
  :after,
  :before {
    background-repeat: no-repeat;
    box-sizing: inherit;
  }

  :after,
  :before {
    text-decoration: inherit;
    vertical-align: inherit;
  }

  * {
    padding: 0;
    margin: 0;
  }

  a {
    background-color: transparent;
  }

  a:active,
  a:hover {
    outline-width: 0;
  }

  .v-application .mb-4 {
    margin-bottom: 16px !important;
  }

  .v-application .mb-8 {
    margin-bottom: 32px !important;
  }

  .v-application .px-2 {
    padding-right: 8px !important;
    padding-left: 8px !important;
  }

  .v-application .px-4 {
    padding-right: 16px !important;
    padding-left: 16px !important;
  }

  .v-application .py-3 {
    padding-top: 12px !important;
    padding-bottom: 12px !important;
  }

  .v-application .text-center {
    text-align: center !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  .flex {
    flex: 1 1 auto;
    max-width: 100%;
  }

  /*! CSS Used from: Embedded */
  *,
  ::before,
  ::after {
    box-sizing: border-box;
    border-width: 0;
    border-style: solid;
    border-color: currentColor;
  }

  ::before,
  ::after {
    --tw-content: '';
  }

  h3 {
    font-size: inherit;
    font-weight: inherit;
  }

  a {
    color: inherit;
    text-decoration: inherit;
  }

  h3 {
    margin: 0;
  }

  :disabled {
    cursor: default;
  }

  *,
  ::before,
  ::after {
    --tw-border-spacing-x: 0;
    --tw-border-spacing-y: 0;
    --tw-translate-x: 0;
    --tw-translate-y: 0;
    --tw-rotate: 0;
    --tw-skew-x: 0;
    --tw-skew-y: 0;
    --tw-scale-x: 1;
    --tw-scale-y: 1;
    --tw-scroll-snap-strictness: proximity;
    --tw-ring-offset-width: 0px;
    --tw-ring-offset-color: #fff;
    --tw-ring-color: rgb(59 130 246 / 0.5);
    --tw-ring-offset-shadow: 0 0 #0000;
    --tw-ring-shadow: 0 0 #0000;
    --tw-shadow: 0 0 #0000;
    --tw-shadow-colored: 0 0 #0000;
  }

  .mb-4 {
    margin-bottom: 1rem !important;
  }

  .mb-8 {
    margin-bottom: 2rem !important;
  }

  .flex {
    display: flex !important;
  }

  .min-h-full {
    min-height: 100% !important;
  }

  /* .w-full {
    width: 100% !important;
  } */

  .animate-pulse-fast {
    animation: pulse 0.5s cubic-bezier(.5, 0, 1, 1) infinite alternate !important;
  }

  .cursor-pointer {
    cursor: pointer !important;
  }

  .justify-between {
    justify-content: space-between !important;
  }

  .border {
    border-width: 1px !important;
  }

  .bg-os_bg {
    --tw-bg-opacity: 1 !important;
    /* background-color: rgb(33 47 60 / var(--tw-bg-opacity)) !important; */
  }

  .bg-os_orange {
    --tw-bg-opacity: 1 !important;
    background-color: rgb(230 126 34 / var(--tw-bg-opacity)) !important;
  }

  .bg-os_black {
    --tw-bg-opacity: 1 !important;
    background-color: rgb(41 44 49 / var(--tw-bg-opacity)) !important;
  }

  .p-2 {
    padding: 0.5rem !important;
  }

  .px-2 {
    padding-left: 0.5rem !important;
    padding-right: 0.5rem !important;
  }

  .px-4 {
    padding-left: 1rem !important;
    padding-right: 1rem !important;
  }

  .py-3 {
    padding-top: 0.75rem !important;
    padding-bottom: 0.75rem !important;
  }

  .text-center {
    text-align: center !important;
  }

  .text-xs {
    font-size: 0.75rem !important;
    line-height: 1rem !important;
  }

  .text-sm {
    font-size: 0.875rem !important;
    line-height: 1.25rem !important;
  }

  .text-3xl {
    font-size: 1.875rem !important;
    line-height: 2.25rem !important;
  }

  .font-bold {
    font-weight: 700 !important;
  }

  .text-white {
    --tw-text-opacity: 1 !important;
    color: rgb(255 255 255 / var(--tw-text-opacity)) !important;
  }

  .text-os_blinking_green {
    --tw-text-opacity: 1 !important;
    color: rgb(40 167 69 / var(--tw-text-opacity)) !important;
  }

  .text-red-500 {
    --tw-text-opacity: 1 !important;
    color: rgb(239 68 68 / var(--tw-text-opacity)) !important;
  }

  .hover\:bg-os_menu_yellow:hover {
    --tw-bg-opacity: 1 !important;
    background-color: rgb(255 204 0 / var(--tw-bg-opacity)) !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  h3 {
    margin: 0;
    padding: 0;
  }

  h3 {
    font-size: 100%;
    font-weight: 400;
  }

  *,
  :after,
  :before {
    box-sizing: inherit;
  }

  a {
    color: #281713;
    cursor: pointer;
    text-decoration: none;
  }

  a:hover {
    color: #363636;
  }

  .mb-4 {
    margin-bottom: 1rem !important;
  }

  .p-2 {
    padding: .5rem !important;
  }

  .px-2 {
    padding-left: .5rem !important;
  }

  .px-2 {
    padding-right: .5rem !important;
  }

  .py-3 {
    padding-top: .75rem !important;
    padding-bottom: .75rem !important;
  }

  .px-4 {
    padding-left: 1rem !important;
  }

  .px-4 {
    padding-right: 1rem !important;
  }

  /*! CSS Used from: Embedded */
  *,
  ::before,
  ::after {
    box-sizing: border-box;
    border-width: 0;
    border-style: solid;
    border-color: currentColor;
  }

  ::before,
  ::after {
    --tw-content: '';
  }

  h3 {
    font-size: inherit;
    font-weight: inherit;
  }

  a {
    color: inherit;
    text-decoration: inherit;
  }

  h3 {
    margin: 0;
  }

  :disabled {
    cursor: default;
  }

  *,
  ::before,
  ::after {
    --tw-border-spacing-x: 0;
    --tw-border-spacing-y: 0;
    --tw-translate-x: 0;
    --tw-translate-y: 0;
    --tw-rotate: 0;
    --tw-skew-x: 0;
    --tw-skew-y: 0;
    --tw-scale-x: 1;
    --tw-scale-y: 1;
    --tw-scroll-snap-strictness: proximity;
    --tw-ring-offset-width: 0px;
    --tw-ring-offset-color: #fff;
    --tw-ring-color: rgb(59 130 246 / 0.5);
    --tw-ring-offset-shadow: 0 0 #0000;
    --tw-ring-shadow: 0 0 #0000;
    --tw-shadow: 0 0 #0000;
    --tw-shadow-colored: 0 0 #0000;
  }

  .mb-4 {
    margin-bottom: 1rem !important;
  }

  .mb-8 {
    margin-bottom: 2rem !important;
  }

  .flex {
    display: flex !important;
  }

  .min-h-full {
    min-height: 100% !important;
  }

  /* .w-full {
    width: 100% !important;
  } */

  .animate-pulse-fast {
    animation: pulse 0.5s cubic-bezier(.5, 0, 1, 1) infinite alternate !important;
  }

  .cursor-pointer {
    cursor: pointer !important;
  }

  .justify-between {
    justify-content: space-between !important;
  }

  .border {
    border-width: 1px !important;
  }

  .bg-os_bg {
    --tw-bg-opacity: 1 !important;
    /* background-color: rgb(33 47 60 / var(--tw-bg-opacity)) !important; */
  }

  .bg-os_orange {
    --tw-bg-opacity: 1 !important;
    background-color: rgb(230 126 34 / var(--tw-bg-opacity)) !important;
  }

  .bg-os_black {
    --tw-bg-opacity: 1 !important;
    background-color: rgb(41 44 49 / var(--tw-bg-opacity)) !important;
  }

  .p-2 {
    padding: 0.5rem !important;
  }

  .px-2 {
    padding-left: 0.5rem !important;
    padding-right: 0.5rem !important;
  }

  .px-4 {
    padding-left: 1rem !important;
    padding-right: 1rem !important;
  }

  .py-3 {
    padding-top: 0.75rem !important;
    padding-bottom: 0.75rem !important;
  }

  .text-center {
    text-align: center !important;
  }

  .text-xs {
    font-size: 0.75rem !important;
    line-height: 1rem !important;
  }

  .text-sm {
    font-size: 0.875rem !important;
    line-height: 1.25rem !important;
  }

  .text-3xl {
    font-size: 1.875rem !important;
    line-height: 2.25rem !important;
  }

  .font-bold {
    font-weight: 700 !important;
  }

  .text-white {
    --tw-text-opacity: 1 !important;
    color: rgb(255 255 255 / var(--tw-text-opacity)) !important;
  }

  .text-os_blinking_green {
    --tw-text-opacity: 1 !important;
    color: rgb(40 167 69 / var(--tw-text-opacity)) !important;
  }

  .text-red-500 {
    --tw-text-opacity: 1 !important;
    color: rgb(239 68 68 / var(--tw-text-opacity)) !important;
  }

  .hover\:bg-os_menu_yellow:hover {
    --tw-bg-opacity: 1 !important;
    background-color: rgb(255 204 0 / var(--tw-bg-opacity)) !important;
  }

  /*! CSS Used from: Embedded */
  * {
    box-sizing: border-box;
  }

  .pi {
    font-size: 1rem;
  }

  /*! CSS Used from: Embedded */
  .pi {
    font-family: "primeicons";
    speak: none;
    font-style: normal;
    font-weight: 400;
    font-variant: normal;
    text-transform: none;
    line-height: 1;
    display: inline-block;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }

  .pi:before {
    --webkit-backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
  }

  .pi-circle-fill:before {
    content: "\e9dd";
  }

  .pi-sign-in:before {
    content: "\e970";
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  .v-application a {
    color: #1976d2;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used from: Embedded */
  ::-webkit-scrollbar-button {
    width: 0;
    height: 0;
  }

  ::-webkit-scrollbar-thumb {
    background: #6c757d;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-thumb:active,
  ::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
  }

  ::-webkit-scrollbar-track {
    background: #f4f5fa;
    border: 0 #f4f5fa;
    border-radius: 50px;
  }

  ::-webkit-scrollbar-track:active,
  ::-webkit-scrollbar-track:hover {
    background: #f4f5fa;
  }

  ::-webkit-scrollbar-corner {
    background: transparent;
  }

  div,
  h3 {
    --tw-text-opacity: 1 !important;
    font-family: Ubuntu, sans-serif !important;
  }

  /*! CSS Used keyframes */
  @keyframes pulse {
    50% {
      opacity: .5;
    }
  }

  @keyframes pulse {
    50% {
      opacity: .5;
    }
  }
</style>
@endsection

@section('content')
<div class="bg-os_bg min-h-full mt-4">
  <div class="px-4">
    <div class="bg-os_black border p-2 mb-8">
      <div class="flex px-2 justify-between">
        @if ($is_online)
        <h3 class="text-red-500 font-bold text-sm">LIVE</h3>
        <i class="fa-solid fa-circle-dot text-xs text-os_blinking_green animate-pulse-fast"></i>
        @else
        <h3 class="text-white text-sm">OFFLINE</h3>
        <i class="fa-solid fa-circle-dot text-xs animate-pulse-fast"></i>
        @endif
      </div>
      <h3 class="text-white font-bold text-3xl text-center mb-4">
        SABONG WORLD WIDE
      </h3> <a href="/play" class="">
        <div class="py-3 bg-os_orange text-white text-center text-sm hover:bg-os_menu_yellow cursor-pointer" style="width:100% !important;">
          <h3 class="font-bold"><i class="fa-solid fa-arrow-up-right-from-square"></i> ENTER GAME CONSOLE
          </h3>
        </div>
      </a>
    </div>
    @if (Auth::user()->role_id == 1)
    <div class="bg-os_black border p-2 mb-8">
      <div class="flex px-2 justify-between">
        <h3 class="text-white text-sm">ONLINE</h3>
        <i class="fa-solid fa-circle-dot text-xs animate-pulse-fast"></i>
      </div>
      <h3 class="text-white font-bold text-3xl text-center mb-4">
        TRANSACTIONS
      </h3> <a href="/transactions" class="">
        <div class="py-3 bg-os_orange text-white text-center text-sm hover:bg-os_menu_yellow cursor-pointer" style="width:100% !important;">
          <h3 class="font-bold"><i class="fa-solid fa-arrow-up-right-from-square"></i> ENTER TRANS PAGE
          </h3>
        </div>
      </a>
    </div>
    @endif
    <div class="bg-os_black border p-2 mb-8" style="display: none;">
      <div class="flex px-2 justify-between">
        <h3 class="text-red-500 font-bold text-sm">LIVE</h3>
          <i class="fa-solid fa-circle-dot text-xs text-os_blinking_green animate-pulse-fast"></i>
      </div>
      <h3 class="text-white font-bold text-3xl text-center mb-4">
        ACTION MOVIE
      </h3> <a href="/watch/movie" class="">
        <div class="py-3 bg-os_orange text-white text-center text-sm hover:bg-os_menu_yellow cursor-pointer" style="width:100% !important;">
          <h3 class="font-bold"><i class="fa-solid fa-arrow-up-right-from-square"></i> ENTER MOVIE CONSOLE
          </h3>
        </div>
      </a>
    </div>
  </div>
</div>
@endsection

@section('additional-scripts')

@endsection
