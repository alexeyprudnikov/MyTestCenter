export function getFormData(form) {
  return Array.from(new FormData(form).entries());
}

export function isFormValid(form) {
  form.classList.add('was-validated');
  return form.checkValidity();
}

export async function get(url = "") {
  return await fetch(url);
}

export async function postData(url = "", payload = {}) {
  const response = await fetch(url, {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
    },
    body: new URLSearchParams(payload),
  });
  if (response.status !== 200) {
    throw new Error('something wrong');
  }
  return response;
}

export async function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}
