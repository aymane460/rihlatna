const userIcon = document.getElementById('userIcon');
  const modal = document.getElementById('authModal');
  const closeModal = document.getElementById('closeModal');

  const loginTab = document.getElementById('loginTab');
  const signupTab = document.getElementById('signupTab');
  const loginForm = document.getElementById('loginForm');
  const signupForm = document.getElementById('signupForm');

  userIcon.onclick = () => {
    modal.style.display = 'flex';
  };

  closeModal.onclick = () => {
    modal.style.display = 'none';
  };

  window.onclick = (e) => {
    if (e.target === modal) modal.style.display = 'none';
  };

  loginTab.onclick = () => {
    loginForm.style.display = 'block';
    signupForm.style.display = 'none';
    loginTab.classList.add('active');
    signupTab.classList.remove('active');
  };

  signupTab.onclick = () => {
    signupForm.style.display = 'block';
    loginForm.style.display = 'none';
    signupTab.classList.add('active');
    loginTab.classList.remove('active');
  };
  