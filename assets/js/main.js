$(document).ready(function () {
  // Add a subtle shadow to the navbar once the page is scrolled
  $(window).on('scroll', function () {
    if ($(window).scrollTop() > 10) {
      $('.site-header').css('box-shadow', '0 4px 24px rgba(0,0,0,0.35)');
    } else {
      $('.site-header').css('box-shadow', '0 2px 20px rgba(0,0,0,0.25)');
    }
  });

  // Confirm before any delete link is clicked (extra safety net)
  $('.btn-danger').on('click', function (e) {
    if (!confirm('Are you sure? This action cannot be undone.')) {
      e.preventDefault();
    }
  });

  // Auto-hide success/info alerts after 4 seconds
  setTimeout(function () {
    $('.alert-success, .alert-info').fadeOut(500);
  }, 4000);

  // Close mobile nav menu when a link inside it is clicked
  $('.nav-links a').on('click', function () {
    $('#navLinks').removeClass('open');
  });
});