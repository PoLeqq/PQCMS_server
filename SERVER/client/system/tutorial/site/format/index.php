<?php

// TEGO NIE BLOKUJEMY!!!
// ZAWIERA INFO JAK W OGÓLE STARTOWAĆ PROGRAM PQCMS (ze zmianami config/data.json, więc serio podstawowe,
// jeszcze nie będzie miał(a) zweryfikowanej licencji a co dopiero wygenerowanego tokenu PQCMS)

?>

<!DOCTYPE html>
<html lang="pl-PL">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Poradnik - Start | PQCMS</title>
    <meta name="description" content="Oficjalna strona systemu PQCMS">
    <meta name="keywords" content="">
    <meta name="author" content='Wiktor "PoLeq" Soliński'>
    <meta http-equiv="X-Ua-Compatible" content="IE=edge">
    <link rel="icon" type="image/x-icon" href="../../../../../images/PQCMS.svg">

    <link rel="stylesheet" href="../../../../../bs5/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../tutorial.css">
    <link rel="stylesheet" href="../../../homepage.css">

    <meta content="PQCMS" property="og:title"/>
    <meta content="(#1) Poradnik opisujący, jak połączyć Twój serwer z serwerami PQCMS."
          property="og:description"/>

    <style>
        td, th {
            border: 1px solid black;
            padding: 10px;
        }
    </style>

</head>
<body>

    <noscript>
        UWAGA! Korzystanie ze strony z wyłączonym JavaScriptem uniemożliwia korzystanie ze strony w pełni pięknej i
        funkcjonalnej! Rób jak uważasz!
    </noscript>

    <div id="site-container" class="col-12 p-5">
        <h1>START</h1>
        Tutaj znajdziesz informacje na temat formatowania tekstu w edytorze.
        <section>
            <b>Zanim zobaczysz tabelkę z wszystkimi znacznikami, poświęć chwilę, aby przeczytać ten krótki wstęp.</b>
            <div>
                Formatowanie <s>tekstu</s> <u>to bardzo przydatna funkcja</u> <i>i jedna z zalet</i> <code>PQCMS</code>.
                Podobna jest ona do BBCode, z którym możliwe, że miałeś/aś styczność. Jeżeli nie, to mogę Cię uspokoić, że to nic strasznego, ani trudnego do nauczenia się!
            </div>
        </section>
        <section>
            <div>
                Musisz pamiętać o tym, aby otwierając znacznik zawsze go zamykać (np. pogrubiając teskt: <code>[b]tekst[/b]</code>).
                Nie możesz tutaj zapomnieć o "<code>[/b]</code>", ponieważ tekst nie zostanie prawidłowo sformatowany!
            </div>
        </section>
        <section>
            <div>
                PQCMS oferuje parę skrótów, które ułatwią Ci zmieniać styl tekstu.
                Możesz je wywołać naciskając <code>Ctrl + </code>: b, i, l, s, u
            </div>
        </section>
        <section>
            <table>
                <thead>
                    <tr>
                        <th>Znacznik</th>
                        <th>Wynik</th>
                        <th>Opis</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <pre>[h1]Tekst[/h1]</pre>
                        </td>
                        <td>
                            <h1>Tekst</h1>
                        </td>
                        <td>
                            Nagłówek 1. stopnia
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <pre>[h2]Tekst[/h2]</pre>
                        </td>
                        <td>
                            <h2>Tekst</h2>
                        </td>
                        <td>
                            Nagłówek 2. stopnia
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <pre>[h3]Tekst[/h3]</pre>
                        </td>
                        <td>
                            <h3>Tekst</h3>
                        </td>
                        <td>
                            Nagłówek 3. stopnia
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <pre>[h4]Tekst[/h4]</pre>
                        </td>
                        <td>
                            <h4>Tekst</h4>
                        </td>
                        <td>
                            Nagłówek 4. stopnia
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <pre>[h5]Tekst[/h5]</pre>
                        </td>
                        <td>
                            <h5>Tekst</h5>
                        </td>
                        <td>
                            Nagłówek 5. stopnia
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <pre>[h6]Tekst[/h6]</pre>
                        </td>
                        <td>
                            <h6>Tekst</h6>
                        </td>
                        <td>
                            Nagłówek 6. stopnia
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <pre>[p]Tekst 1[/p][p]Tekst 2[/p]</pre>
                        </td>
                        <td>
                            <p>Tekst</p>
                            <p>Tekst</p>
                        </td>
                        <td>
                            Paragrafy
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <pre>[b]Tekst[/b]</pre>
                        </td>
                        <td>
                            <b>Tekst</b>
                        </td>
                        <td>
                            Pogrubienie
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <pre>[i]Tekst[/i]</pre>
                        </td>
                        <td>
                            <i>Tekst</i>
                        </td>
                        <td>
                            Kursywa
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <pre>[u]Tekst[/u]</pre>
                        </td>
                        <td>
                            <u>Tekst</u>
                        </td>
                        <td>
                            Podkreślenie
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <pre>[s]Tekst[/s]</pre>
                        </td>
                        <td>
                            <s>Tekst</s>
                        </td>
                        <td>
                            Skreślenie
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <pre>[url]https://poleq.pl/[/url]</pre>
                        </td>
                        <td>
                            <a href="https://poleq.pl/">https://poleq.pl/</a>
                        </td>
                        <td>
                            Link
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <pre>[url=https://poleq.pl/]link[/url]</pre>
                        </td>
                        <td>
                            <a href="https://poleq.pl/">link</a>
                        </td>
                        <td>
                            Link z dowolnym tekstem
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <pre>[img]images/kotek.png[/img]</pre>
                        </td>
                        <td>
                            <img src="images/kotek.png" style="width: 100px">
                        </td>
                        <td>
                            Obrazek
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <pre>[list=1]
    [*]element listy
    [*]element listy
[/list]</pre>
                        </td>
                        <td>
                            <ol>
                                <li>element listy</li>
                                <li>element listy</li>
                            </ol>
                        </td>
                        <td>
                            Lista numerowana
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <pre>[list=a]
    [*]element listy
    [*]element listy
    [*]element listy
[/list]</pre>
                        </td>
                        <td>
                            <ol type="a">
                                <li>element listy</li>
                                <li>element listy</li>
                                <li>element listy</li>
                            </ol>
                        </td>
                        <td>
                            Lista alfabetyczna
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <pre>[list]
    [*]element listy
    [*]element listy
    [*]element listy
[/list]</pre>
                        </td>
                        <td>
                            <ul>
                                <li>element listy</li>
                                <li>element listy</li>
                                <li>element listy</li>
                            </ul>
                        </td>
                        <td>
                            Lista nieuporządkowana
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <pre>[code]code[/code]</pre>
                        </td>
                        <td>
                            <code>code</code>
                        </td>
                        <td>
                            Blok kodu
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <pre>[youtube]dQw4w9WgXcQ[/youtube]</pre>
                        </td>
                        <td>
                            <iframe width="336" height="189" src="//www.youtube-nocookie.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe>
                        </td>
                        <td>
                            Odtwarzacz youtube (normalnie jest nieco większych rozmiarów)
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <pre>tekst[sub]t[/sub]</pre>
                        </td>
                        <td>
                            tekst<sub>t</sub>
                        </td>
                        <td>
                            Indeks dolny
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <pre>tekst[sup]t[/sup]</pre>
                        </td>
                        <td>
                            tekst<sup>t</sup>
                        </td>
                        <td>
                            Indeks górny
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <pre>Tekst [small]mały[/small]</pre>
                        </td>
                        <td>
                            Tekst <small>mały</small>
                        </td>
                        <td>
                            Pomniejszony tekst
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <pre>[table]
    [tr]
        [th]Nagłówek[/th]
        [th]Nagłówek 2[/th]
    [/tr]
    [tr]
        [td]Komórka[/td]
        [td]Komórka 2[/td]
    [/tr]
    [tr]
        [td]Komórka[/td]
        [td]Komórka 2[/td]
    [/tr]
[/table]</pre>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <th>Nagłówek</th>
                                    <th>Nagłówek 2</th>
                                </tr>
                                <tr>
                                    <td>Komórka</td>
                                    <td>Komórka 2</td>
                                </tr>
                                <tr>
                                    <td>Komórka</td>
                                    <td>Komórka 2</td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            Tabela
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>