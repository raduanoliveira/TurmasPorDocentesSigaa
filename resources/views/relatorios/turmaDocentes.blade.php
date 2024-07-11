
<table>
    <thead>
        <tr>
            <th>Nome Docente / Código Turma</th>
            <th>Componente</th>
            <th>Período</th>
            <th>Qtd Participantes</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dados as $dado)
            <tr>
                <td>{{ $dado->nome }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @isset($dado->turmas)
                @foreach ($dado->turmas as $turma)
                    <tr>
                        <td>{{ $turma->{'codigo-turma'} }}</td>
                        <td>{{ $turma->{'nome-componente'} }}</td>
                        <td>{{ $turma->periodo }}</td>
                        <td>{{ $turma->qtd_participantes}}</td>
                    </tr>
                @endforeach
            @endisset
        @endforeach
    </tbody>
</table>
