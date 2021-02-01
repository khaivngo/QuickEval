# Database

This page explains the database structure.

The core of the database is the tables associated with experiments.

When creating an experiment, a researcher first has to select one or more picture sets from
**pictureSet**: 
the pictures in these sets gets 'looked' up in **picture**.

Depending on the order/randomasation algorithm the reseacher selects during experiment creation:
a order that dictates the order each picture will appear in the experiment is generated, and inserted into **pictureOrder**.
These are then given a **pictureQueue** that defines the queue itself. This is later used to find which picture queue to be used in each
step in a experiment.

**experimentOrder** dictates for each step in a experiment if a image(s) or instructions to show at what steps in the experiment.
This order is set by the researcher during experiment creation.

Måten tabellene vet om det er et bildesett eller en instruksjon ved at enten er fremmednøkkelen for 
en **pictureQueue** satt eller så er **experimentInstruction** satt.
**experimentQueue** mellom **experiment** og **experimentOrder** er definisjonen på køen, og tar vare på hvilke eksperiment 
som har hvilke eksperimentrekkefølger.


The stimulus order algorithms:

same-pair flipped


<div style="width: 100px; height: 100px; background-color: red;">
  {{ 1 + 3 }}
</div>
