<?php
            
                    // Fonction pour formater la date depuis un timestamp Unix
                    function formaterDate($timestamp) {
                        return date("d-m-Y", $timestamp);
                    }

                    // Début de la table HTML
                    echo "<table border='1' cellpadding='10' cellspacing='0'>";
                    echo "<thead><tr>";

                    // Affichage des en-têtes (les clés du premier élément)
                    foreach(array_keys($clan_members[0]) as $entete) {
                        echo "<th>" . htmlspecialchars($entete) . "</th>";
                    }
                    echo "</tr></thead>";

                    // Affichage des lignes du tableau
                    echo "<tbody>";
                    foreach($clan_members as $ligne) {
                        echo "<tr>";
                        foreach($ligne as $cle => $valeur) {
                            // Si la clé est 'join_date', formater la date
                            if ($cle === 'join_date') {
                                echo "<td>" . htmlspecialchars(formaterDate($valeur)) . "</td>";
                            } else {
                                echo "<td>" . htmlspecialchars($valeur) . "</td>";
                            }
                        }
                        echo "</tr>";

                    }
                    echo "</tbody>";
                    echo "</table>";

                    
                ?>