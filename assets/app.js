// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';
import 'bootstrap';

// start the Stimulus application
// import './bootstrap';

const $ = require('jquery');
const summernote = require('summernote');

$('#summernote').summernote({
    toolbar: [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough', 'superscript', 'subscript']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['height', ['height']]
    ]
});

console.log(summernote);

console.log('labas');