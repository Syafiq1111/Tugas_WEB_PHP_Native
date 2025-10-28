document.addEventListener('DOMContentLoaded', function () {

    // FAQ Pertama
    const question1 = document.getElementById('question-1');
    question1.addEventListener('click', function () {
        const answer1 = document.getElementById('answer-1');
        answer1.classList.toggle('active');
    });


    // FAQ Kedua
    const question2 = document.getElementById('question-2');
    question2.addEventListener('click', function () {
        const answer2 = document.getElementById('answer-2');
        answer2.classList.toggle('active');
    });

});