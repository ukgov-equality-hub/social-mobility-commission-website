const circleCharts = document.querySelectorAll('.circle-chart')

circleCharts.forEach((chart) => {
  const percentage = chart.getAttribute('data-percentage');
  const outerBar = chart.querySelector('.outer-bar')
  const bar = chart.querySelector('.bar')
  const outerProgressBar = chart.querySelector('.outer-progress-bar')
  const progressBar = chart.querySelector('.progress-bar')
  const centerPercentage = chart.querySelector('.percentage')

  outerBar.style.strokeDashoffset = `calc(-${percentage} - 5)`;
  bar.style.strokeDashoffset = `calc(-${percentage} - 5)`;
  outerProgressBar.style.strokeDashoffset = `calc(100 - ${percentage} + 4)`;
  progressBar.style.strokeDashoffset = `calc(100 - ${percentage} + 4)`;
  centerPercentage.textContent = percentage + '%';

})
