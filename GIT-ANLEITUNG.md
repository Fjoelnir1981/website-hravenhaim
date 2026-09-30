# Änderungen ansehen und rückgängig machen

Der Ordner enthält das vollständige lokale Git-Repository. Beim Kopieren muss der versteckte Ordner `.git` mitgenommen werden. Ein ZIP der fertigen Website enthält ihn ebenfalls.

## Die gesicherten Stände

- `main`: ursprünglicher Dateistand vor dem Umbau.
- `vor-designumbau`: dauerhaft markierter Ausgangsstand.
- `design/zielbild`: fertig umgebaute Website.

Es wurde nichts auf GitHub hochgeladen. Das bereits vorhandene Remote bleibt unverändert eingetragen.

## Änderungen ansehen

Im Terminal in den Website-Ordner wechseln und ausführen:

```sh
git status
git log --oneline --decorate
git diff --stat vor-designumbau..HEAD
git diff vor-designumbau..HEAD -- index.html
```

## Einen Änderungsschritt rückgängig machen

Zuerst `git status` prüfen. Eigene neue Änderungen vorher committen oder mit `git stash push -u -m "Eigene Änderungen vor Rücknahme"` sichern.

Dann die Kennung des gewünschten Commits aus `git log --oneline` übernehmen:

```sh
git revert COMMIT_KENNUNG
```

`COMMIT_KENNUNG` durch die tatsächliche Kennung ersetzen. Git erstellt einen neuen Commit, der diesen Schritt rückgängig macht. Die Historie bleibt erhalten. Bei voneinander abhängigen Änderungen die jüngsten Schritte zuerst zurücknehmen. Bei Konflikten kann `git revert --abort` die begonnene Rücknahme abbrechen.

## Den Ausgangsstand separat öffnen

Die aktuelle Website bleibt dabei unverändert:

```sh
git worktree add ../website-hravenhaim-vor-umbau -b ansicht/vor-umbau vor-designumbau
```

Der zusätzliche Ordner enthält den ursprünglichen Stand. Existiert dieser Ordner oder Branch bereits, einen anderen Namen wählen. Der Originalstand kann seine ursprünglichen Darstellungsfehler enthalten.

## Den gesamten Umbau zurücknehmen

Nur bei sauberem `git status` und ohne noch offene Rücknahme ausführen:

```sh
git revert --no-commit vor-designumbau..HEAD
git commit -m "Designumbau zurückgenommen"
```

Damit bleiben auch die Umbau-Commits im Verlauf erhalten. `git reset --hard` ist dafür nicht nötig.

## Daten und Zugangsdaten

Die Datenbank, Sitzungen und SMTP-Zugangsdaten gehören nicht zum Git-Verlauf. Git stellt diese Daten nicht wieder her und löscht sie bei einer Design-Rücknahme nicht. Die SQLite-Datenbank muss separat gesichert werden. Ein für Tests ausgecheckter älterer Stand sollte eine eigene Testdatenbank verwenden.
