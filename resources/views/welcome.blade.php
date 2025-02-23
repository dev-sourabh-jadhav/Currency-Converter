<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Currency Converter</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- jQuery and Select2 JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
        }

        .converter-card {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .select2-container .select2-selection--single {
            height: 40px;
            padding: 5px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 35px;
        }

        .form-group label {
            font-weight: bold;
        }

        .currency-icon {
            font-size: 18px;
            margin-right: 10px;
        }

        .result-box {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            padding: 10px;
            border-radius: 8px;
            background-color: #e9ecef;
            margin-top: 15px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="card converter-card">
            <h2 class="text-center">💰 Currency Converter</h2>
            <form id="currencyForm">
                <div class="form-group">
                    <label for="amount">Enter Amount:</label>
                    <input type="number" class="form-control" id="amount" placeholder="Enter amount" required>
                </div>

                <div class="form-group">
                    <label for="fromCurrency">From Currency:</label>
                    <select class="form-control" id="fromCurrency" required>
                        <option value="" selected>Select Currency</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="toCurrency">To Currency:</label>
                    <select class="form-control" id="toCurrency" required>
                        <option value="" selected>Select Currency</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Convert</button>
            </form>

            <div id="result" class="result-box mt-3">
                Converted Amount will appear here.
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $.ajax({
                url: 'https://restcountries.com/v3.1/all',
                method: 'GET',
                success: function(data) {
                    let options = [
                    `<option value="" selected disabled>Select a Currency</option>`]; // Default empty option

                    data.forEach(function(country) {
                        if (country.currencies) {
                            $.each(country.currencies, function(code, currency) {
                                let flag = country.flags?.png || '';
                                let countryName = country.name.common;

                                options.push(`<option value="${code}" data-flag="${flag}" data-country="${countryName}">
                            ${countryName} (${code})
                        </option>`);
                            });
                        }
                    });

                    $('#fromCurrency, #toCurrency').html(options.join('')).select2({
                        templateResult: formatCountry,
                        templateSelection: formatCountry,
                        placeholder: "Select a Currency",
                        allowClear: true // Allow clearing selection
                    });
                },
                error: function(error) {
                    console.log('Error fetching country data:', error);
                }
            });

            function formatCountry(state) {
                if (!state.id) return state.text;
                let flagUrl = $(state.element).attr('data-flag') || '';
                return $(
                    `<span><img src="${flagUrl}" width="20" height="15" style="margin-right:5px;"> ${state.text}</span>`
                    );
            }

            $('#currencyForm').on('submit', function(e) {
                e.preventDefault();
                var amount = $('#amount').val();
                var fromCurrency = $('#fromCurrency').val();
                var toCurrency = $('#toCurrency').val();

                $.ajax({
                    url: '/convert-currency',
                    method: 'POST',
                    data: {
                        amount: amount,
                        from: fromCurrency,
                        to: toCurrency
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#result').html(
                            `<p>${amount} ${fromCurrency} = <strong>${response.convertedAmount}</strong> ${toCurrency}</p>`
                            );
                    },
                    error: function(error) {
                        console.log('Error converting currency:', error);
                    }
                });
            });
        });
    </script>

</body>

</html>
