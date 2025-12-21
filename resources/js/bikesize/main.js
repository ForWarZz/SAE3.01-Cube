const height = document.querySelector('#user-height');
const stepLength = document.querySelector('#user-inseam');
const validateBut = document.querySelector('#calculate-size-btn');
const res = document.querySelector('#size-result-text');
const sizeList  = ["XS", "S", "M", "L", "XL"];

function setBorder(element, isError){
    if (isError) {
        element.style.border = "2px solid red"; 
    } else {
        element.style.border = ""; 
    }
};

validateBut.addEventListener('click', (e) => {
    if (height.value.trim() === "") {
        setBorder(height, true);
    } else {
        setBorder(height, false);
    }

    if (stepLength.value.trim() === "") {
        setBorder(stepLength, true);
    } else {
        setBorder(stepLength, false);
    }
    
    if (height.value.trim() !== "" && stepLength.value.trim() !== "") {
        
        let heightNumber = parseInt(height.value, 10);
        let stepLengthNumber = parseInt(stepLength.value, 10);
        let ratio = stepLengthNumber / heightNumber;
        
        if (heightNumber < 160)
            res.innerText = "XS"
        else if (heightNumber < 170)
            res.innerText = "S"
        else if (heightNumber < 180)
            res.innerText = "M"
        else if (heightNumber < 190)
            res.innerText = "L"
        else
            res.innerText = "XL"

        if (ratio < 0.44 || ratio > 0.48){

            let index;

            for (let i=0; i<sizeList.length; i++){
                if (sizeList[i] === res.innerText)
                    index = i
            }

            if (ratio < 0.44 && res.innerText != "XS")
                res.innerText = sizeList[index-1]

            else if (ratio > 0.48 && res.innerText != "XL")
                res.innerText = sizeList[index+1]
        }

        res.parentElement.classList.remove('hidden');
    }
});