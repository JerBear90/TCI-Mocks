<?php
$data = stringency_table();
$annual_data = annual_table();
?>
<div id="waire-calculator">
    <div id="waire-1" class="step">
        <div class="hd d-flex">
            <span class="icon">1</span>
            <h3>Warehouse Information</h3>
        </div>
        <div class="bd">
            <div class="form-group">
                <label>Square Footage of Warehouse</label>
                <select name="phase" class="form-control">
                    <option value="" selected>Please Select</option>
                    <option value="phase1">≥250,000 sq ft</option>
                    <option value="phase2">≥150,000-249,999 sq ft</option>
                    <option value="phase3">≥100,000-149,999 sq ft</option>
                </select>
            </div>

            <div class="d-flex">
                <div class="form-group">
                    <label>Compliance Year</label>
                    <select class="form-control w-50" name="year">
                        <option value="">--</option>
                        <option value="2022">2022</option>
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                        <option value="2026+">2026+</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Workdays per year</label>
                    <input type="text" name="workdays" class="form-control"/>
                </div>
            </div>
            
            <div class="stringency-value">
                Stringency: <span class="value">0.000000</span>
            </div>
        </div>
    </div>

    <div id="waire-2" class="step">
        <div class="hd d-flex">
            <span class="icon">2</span>
            <h3>Trip Information</h3>
        </div>
        <div class="bd">
            <div class="d-flex">
                <div class="form-group">
                    <label>Yearly Class 8 Trips</label>
                    <input type="text" class="form-control" name="class-8" value=""/>
                </div>
                <div class="form-group">
                    <label>Daily 2b-7 Trips</label>
                    <input type="text" class="form-control" name="daily-2b7" value=""/>
                </div>
            </div>

            <div class="response d-flex">
                <label>Estimated WATTS Points:</label>
                <span class="value watts"></span>
            </div>
            <div class="response d-flex">
                <label>Estimated WAIRE Points:</label>
                <span class="value waire"></span>
            </div>
        </div>
    </div>

    <div id="waire-3" class="step">
        <div class="hd d-flex">
            <span class="icon">3</span>
            <h3>Potential Mitigation Fee</h3>
        </div>
        <div class="bd">
            <div class="value mitigation-value" data-name="mitigation"></div>
        </div>
        <a class="reset" href="#">Reset</a>
    </div>
</div>

<script>
    var d = console.log;
    jQuery(document).ready( function($) {
        var data = <?php echo json_encode($data); ?>;
        var annual_data = <?php echo json_encode($annual_data); ?>;
        $(document).on( 'keyup change', '#waire-calculator .step :input', function() {
            var phase = $('[name=phase]').val();
            var year = $('[name=year]').val();
            var workdays = $('[name=workdays]').val();
            
            var class8 = $('[name="class-8"]').val();
            var daily = $('[name="daily-2b7"]').val();
            
            var watts =  parseInt(daily) * workdays + 2.5 * parseInt(class8);
            
            if( isNaN(watts) ) watts = '';
            $('.watts').text( Math.ceil(watts) );

            var stringency = 0; 
            
            if( year && phase) {
                data.forEach( item=> {
                    if( item.year == year ) {
                        stringency = item[phase];
                    }
                })
                annual_data.forEach( item=> {
                    if( item.year == year ) {
                        annual = item[phase];
                    }
                })
                annual = 1;
                d('stringency:',stringency);
                d('annual:',annual);
                d('watts:',watts);
                var waire = watts * stringency * annual;
                d('waire:',waire);
                //var waire = (((index3(arr1xC24E28, 0, 0, 4, 2, (match((year), row1xB24B28, 0, 4, (0))), (match_str((footage), col1xC23E23, 0, 2, (0))))) * (watts)));
                if( !isNaN(waire) && waire ) {
                    $('.waire').text( waire.toFixed(1) );

                    var mitigation = (waire * 1000)
                    mitigation = parseFloat( mitigation.toFixed(2) ).toLocaleString("en-US");
                    d('mitigation:',mitigation);
                    var parts = mitigation.split('.');
                    d('parts:',parts);
                    if( parts.length == 2 ) {
                        parts[1] = (''+parts[1]).padEnd(2,'0');
                        mitigation = parts.join('.');
                    }
                    $('.mitigation-value').html( '$'+mitigation );
                } else {
                    $('.mitigation-value').html( '$0.00' );
                    $('.waire').text( '0' );
                }
                if( stringency ) {
                    $('.stringency-value .value').text(stringency);
                    $('.stringency-value').css('visibility','visible' );
                } else {
                    $('.stringency-value').css('visibility','hidden' );
                }
                
            }
        });
        
        $(document).on( 'click', '#waire-calculator .reset', function() {
            $('#waire-calculator input, #waire-calculator select').val('');
            
            $('.stringency-value').css('visibility','hidden' );
            $('.watts').text('');
            $('.waire').text('0');
            $('.mitigation-value').text('$0.00');
            return false;
        })
    });
</script>