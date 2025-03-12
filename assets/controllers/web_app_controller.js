import {Controller} from '@hotwired/stimulus';
import {getFormData, postData, sleep, isFormValid} from '../utils.js';

export default class extends Controller {
  static targets = [ "content" ];
  connect() {
    this.loaderText = '<div class="mb-3">Checking and preparing your test\'s participation...</div>';
    this.requestText = '<div class="mb-3">AI will be asking for generating a new dataset...</div>';
    this.checkedText = '<div class="mb-3">Sending test data to check...</div>';
    this.spinner = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
  }
  onInitUser(event) {
    event.preventDefault();
    event.stopPropagation();
    const form = event.target;
    if (!isFormValid(form)) {
      return;
    }

    this.contentTarget.innerHTML = this.loaderText + this.spinner;
    sleep(2000).then(
      () => {
        const payload = getFormData(form);
        postData('/load_saved', payload)
          .then(response => response.json())
          .then(data => {
            const isEmpty = (data.content ?? '') === '';
            if (!isEmpty) {
              this.contentTarget.innerHTML = data.content;
            }
            return isEmpty;
          })
          .then(isEmpty => {
            if (isEmpty) {
              this.contentTarget.innerHTML = this.requestText + this.spinner;
              postData('/load_ai', payload)
                .then(response => response.json())
                .then(data => {
                  this.contentTarget.innerHTML = data.content ?? 'no content';
                });
            }
          })
          .catch(error => {
            this.contentTarget.innerHTML = error;
          });
      }
    );
  }

  onSubmitAnswers(event) {
    event.preventDefault();
    this.contentTarget.innerHTML = this.checkedText + this.spinner;
    sleep(2000).then(
      () => {
        const payload = getFormData(event.target);
        postData('/check', payload)
          .then(response => response.json())
          .then(data => {
            this.contentTarget.innerHTML = data.content ?? 'no content';
          });
      }
    );
  }
}
