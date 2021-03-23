import BaseRepository from "../abstracts/BaseRepository.js";
import CompanyEntity from "../entities/CompanyEntity.js";

class CompanyRepository extends BaseRepository {
    private static instance: CompanyRepository;

    private constructor() {
        super();
    }

    public static getInstance(): CompanyRepository {
        if (!CompanyRepository.instance) {
            CompanyRepository.instance = new CompanyRepository();
        }

        return CompanyRepository.instance;
    }

    public async create(company: CompanyEntity): Promise<void> {
        this.connection.execute("INSERT INTO COMPANIES(NAME, TOKEN) VALUES (?, ?)", [company.NAME, company.TOKEN]);
    }

    public async read(id: number): Promise<CompanyEntity> {
        const [result]: any = await this.connection.execute("SELECT * FROM COMPANIES WHERE ID = ?", [id]);
        const company = new CompanyEntity();
        company.ID = result[0].ID;
        company.NAME = result[0].NAME;
        company.TOKEN = result[0].TOKEN;

        return company;
    }

    public async update(company: CompanyEntity): Promise<void> {
        this.connection.execute("UPDATE COMPANIES SET NAME = ?, TOKEN = ? WHERE ID = ?", [company.NAME, company.TOKEN, company.ID]);
    }

    public async delete(company: CompanyEntity): Promise<undefined> {
        this.connection.execute("DELETE FROM COMPANIES WHERE ID = ?", [company.ID]);
        return undefined;
    }

    public async getCompanyByToken(token: string): Promise<CompanyEntity | null> {
        const [result]: any = await this.connection.execute("SELECT * FROM COMPANIES WHERE TOKEN = ?", [token]);

        if (result.length == 0) return null;

        const company = new CompanyEntity();
        company.ID = result[0].ID;
        company.NAME = result[0].NAME;
        company.TOKEN = result[0].TOKEN;

        return company;
    }

    public async getCompanyByName(name: string): Promise<CompanyEntity | null> {
        const [result]: any = await this.connection.execute("SELECT * FROM COMPANIES WHERE NAME = ?", [name]);

        if (result.length == 0) return null;

        const company = new CompanyEntity();
        company.ID = result[0].ID;
        company.NAME = result[0].NAME;
        company.TOKEN = result[0].TOKEN;

        return company;
    }
}

export default CompanyRepository;