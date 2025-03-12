import {Controller} from '@hotwired/stimulus';
import {get} from '../utils.js';

export default class extends Controller {
  static targets = [ "content" ];
  connect() {
    this.loaderText = '<div class="mb-3">Request wird an die API gesendet und bearbeitet...</div>';
    this.spinner = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
  }

  onRequestRecipes(event) {
    event.preventDefault();
    this.contentTarget.innerHTML = this.loaderText + this.spinner;
    get('/test_gemini/proceed/recipes')
      .then(response => response.json())
      .then(data => {
        this.contentTarget.innerHTML = data.content ?? 'Error: no content';
      });
  }

  onRequestHotels(event) {
    event.preventDefault();
    this.contentTarget.innerHTML = this.loaderText + this.spinner;
    get('/test_gemini/proceed/hotels')
      .then(response => response.json())
      .then(data => {
        this.contentTarget.innerHTML = data.content ?? 'Error: no content';
      });
  }
}
