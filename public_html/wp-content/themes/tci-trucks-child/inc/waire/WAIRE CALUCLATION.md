WAIRE CALUCLATION
=================

https://tcitransportation.com/waire/
Esimated WATT points:
     
     2 * ( daily_class_2b7 * workdays             +              2.5 * class8 * 365)
     
     notes:
     - I do not know where the 2* comes from
     - copied from existing calculator here:
        https://leanmh.com/aqmd-rule-2305-fee-calculator/
    
    - tests of this field produce the same values on both the old & new calculator

Estimate WAIRE pionts:
    
    WATTS * Stringency * Annual Variable
    
    notes:
    - formula & values table taken frohttps://www.usgain.com/resources/education-center/californias-new-waire-program/
    - ** values do not match old calculator:
        - old calculator does not appear to include the "annual vairable"
        - thus the results from the new calculator will be a fraction of the old,
        based on the anual variable table.
            - results start to match up after 2024 as annaul variable becomes set to 1.

    - Stringency:
    year,phase1,phase2,phase3
    2022,0.000825,0,0
    2023,0.001675,0.000825,0
    2024,0.0025,0.001675,0.000825
    2025,0.0025,0.0025,0.001675
    2026+,0.0025,0.0025,0.0025

    - Annual variable
    year,phase1,phase2,phase3
    2022,0.33,0,0
    2023,0.67,0.33,0
    2024,1,0.67,0.33
    2025,1,1,0.67
    2026+,1,1,1

Mitigation fee:
    - taken at $1000 per WAIRE point