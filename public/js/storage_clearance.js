$('#check-all').click(function (event) {
    if (this.checked) {
        // Iterate each checkbox
        $(':checkbox').each(function () {
            if (!this.checked){
                this.checked = true;
                assignValueToSubtotal(this.id)
            }
        });
    } else {
        $(':checkbox').each(function () {
            if (this.checked){
                this.checked = false;
                assignValueToSubtotal(this.id)
            }
        });
    }
});

function assignValueToSubtotal(id) {
    if (id == "check-all"){
        return
    }
    var amountId = 'cargoAmount'+id;
    var amountArray = document.getElementById(amountId).textContent.split(" ");
    var amount = Number(amountArray[0])
    var currency = amountArray[1]

    var exchangeRate = Number(document.querySelector("#exchangeRateTxt").innerHTML.replace(/,/g, ''));
    var totalAmount = Number(document.querySelector("#totalAmountTxt").innerHTML.replace(/,/g, ''));
    var vatAmount = Number(document.querySelector("#amountVATTxt").innerHTML.replace(/,/g, ''));

    var subTotalUsdId = document.getElementById('subTotalUsdTxt');
    var subTotalTzsId = document.getElementById('subTotalTzsTxt');
    var totalAmountId = document.getElementById('totalAmountTxt');
    var amountVATId = document.getElementById('amountVATTxt');

    var currentAmountTzs = Number(subTotalTzsId.innerHTML.replace(/,/g, ''));
    var currentAmountUsd = Number(subTotalUsdId.innerHTML.replace(/,/g, ''));

    internationalNumberFormat = new Intl.NumberFormat('en-US')
    if ((document.getElementById(id).checked)) {
        if(currency == 'TZS'){
            // insert amount in a text
            subTotalTzsId.innerHTML = internationalNumberFormat.format(currentAmountTzs+amount)

            // add 15% VAT and insert
            amountVATId.innerHTML = internationalNumberFormat.format(vatAmount+(amount*15)/100)

            // insert total amount
            totalAmountId.innerHTML = internationalNumberFormat.format(totalAmount+amount+(amount*15)/100)

        }else{
            // insert amount in a text
            subTotalUsdId.innerHTML = internationalNumberFormat.format(currentAmountUsd+amount)
            // subTotalTzsId.innerHTML = currentAmountTzs+(amount*exchangeRateTxt)

            // deduct 15% VAT and insert
            amountVATId.innerHTML = internationalNumberFormat.format(vatAmount+((amount*exchangeRate)*15)/100)

            // insert total amount
            totalAmountId.innerHTML = internationalNumberFormat.format(totalAmount+(amount*exchangeRate)+((amount*exchangeRate)*15)/100)
        }
    } else {
        if(currency == 'TZS'){
            // deduct and insert amount into text
            subTotalTzsId.innerHTML = internationalNumberFormat.format(currentAmountTzs-amount)

            // deduct 15% from amount
            amountVATId.innerHTML = internationalNumberFormat.format(vatAmount-(amount*15)/100)

            totalAmountId.innerHTML = internationalNumberFormat.format(totalAmount-amount-(amount*15)/100)
        }else{
            // deduct and insert into text
            subTotalUsdId.innerHTML = internationalNumberFormat.format(currentAmountUsd-amount)
            // subTotalTzsId.innerHTML = currentAmountTzs-(amount*exchangeRateTxt)

            // deduct 15% from amount
            amountVATId.innerHTML = internationalNumberFormat.format(vatAmount-((amount*exchangeRate)*15)/100)

            totalAmountId.innerHTML = internationalNumberFormat.format(totalAmount-(amount*exchangeRate)-((amount*exchangeRate)*15)/100)
        }
    }
}
