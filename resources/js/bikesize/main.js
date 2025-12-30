const container = document.querySelector('#bike-calculator-container');

if (container) {
    const inputHeight = document.querySelector('#client-height');
    const inputInseam = document.querySelector('#client-inseam');
    const resultBox = document.querySelector('#result-box');
    const resultText = document.querySelector('#result-text');
    const resultDetails = document.querySelector('#result-details');

    const sizeChart = JSON.parse(container.dataset.chart);

    container.addEventListener('submit', (e) => {
        e.preventDefault();

        const height = parseInt(inputHeight.value);
        const inseam = parseInt(inputInseam.value);

        const matchingSizes = sizeChart.filter((size) => {
            const min = size.taille_min || 0;
            const max = size.taille_max || 300;
            return height >= min && height <= max;
        });

        resultBox.classList.remove('hidden');
        resultDetails.classList.remove('hidden');

        const isKid = height < 150;

        if (matchingSizes.length > 0) {
            resultBox.className = 'mt-2 rounded-md border-l-4 border-green-500 bg-white p-4 shadow-sm';
            const sizesNames = matchingSizes.map((s) => s.nom_taille).join(' ou ');

            resultText.innerText = `Taille recommandée : ${sizesNames}`;
            resultText.className = 'text-xl font-bold text-green-700';

            if (isKid) {
                resultDetails.innerText = `Basé sur votre taille (${height}cm). Pour les enfants, la taille globale prime sur l'entrejambe.`;
            } else {
                const theoreticalFrameSize = Math.round(inseam * 0.66);
                resultDetails.innerText = `Basé sur votre taille (${height}cm). Votre entrejambe suggère un cadre théorique de ${theoreticalFrameSize}cm (Route/Gravel).`;
            }
        } else {
            resultBox.className = 'mt-2 rounded-md border-l-4 border-orange-500 bg-white p-4 shadow-sm';
            resultText.innerText = 'Aucune taille standard trouvée.';
            resultText.className = 'text-md font-bold text-orange-700';
            resultDetails.innerText = 'Vos mensurations semblent hors des standards habituels pour ce modèle.';
        }
    });
}
