import Modal from 'bootstrap/js/dist/modal';

document.addEventListener('DOMContentLoaded', function(){
    new Modal('#jftModal');
    const iframe = document.getElementById('jftIframe');
    iframe.onload = function() {
        const iframeDoc = iframe.contentWindow.document;
        const contentHeight = iframeDoc.body.scrollHeight;
        iframe.style.height = contentHeight + 'px';
    };
});
