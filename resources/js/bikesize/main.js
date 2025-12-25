const form = document.querySelector('#bike-size-form');
const height = document.querySelector('#user-height');
const stepLength = document.querySelector('#user-inseam');
const res = document.querySelector('#size-result-text');
const sizeList = ['XS', 'S', 'M', 'L', 'XL'];

if (form) {
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const heightNumber = parseInt(height.value, 10);
        const stepLengthNumber = parseInt(stepLength.value, 10);

        let resultSize;
        if (heightNumber < 160) resultSize = 'XS';
        else if (heightNumber < 170) resultSize = 'S';
        else if (heightNumber < 180) resultSize = 'M';
        else if (heightNumber < 190) resultSize = 'L';
        else resultSize = 'XL';

        const ratio = stepLengthNumber / heightNumber;
        const index = sizeList.indexOf(resultSize);

        if (ratio < 0.44 && index > 0) resultSize = sizeList[index - 1];
        else if (ratio > 0.48 && index < sizeList.length - 1) resultSize = sizeList[index + 1];

        res.innerText = resultSize;
        res.parentElement.classList.remove('hidden');
    });
}
