# ST_App

## Backend

za backend sem uporabil php in mySQL tako kot so bila navodila. <br />
<br />
mapo /backend se nato prestavi v strežnik, ki bo gostoval backend. <br />
V mojem primeru je bil to xampp (apache) <br />
<br />

## Frontend

za frontend pa sem uporabil framework NextJS, ki temelji na React-u z možnostjo SSR (server side rendering). <br />
Za zagon frontenda je potreben Node in yarn. <br />
<br />
Preden zaženemo spletni strežnik je potrebno le nastaviti spremenljivke v env datoteki. <br />
Bolj natančno API ključ za google zemljevide (trenutno je nastavljen moj, ki pa se ga seveda lahko uporabi za testiranje) <br />
in url backend serverja (trenutno je nastavljen na http://localhost/backend) <br />
<br />
Ko je vse to nastavljeno pa se lahko zažene spletni strežnik. <br />
<br />
Preprosto se v konzoli premaknemo v mapo frontend in izvedemo komando `yarn install`, <br />
nato ko se izvajanje konča izvedemo komando `yarn build`, <br />
ob koncu pa še `yarn start`. <br />
<br />
Nato se bo na `http://localhost:3000` zagnal server, kjer je tudi dostop do strani.
