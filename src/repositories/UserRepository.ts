import BaseRepository from "../abstracts/BaseRepository.js";
import UserEntity from "../entities/UserEntity.js";

class UserRepository extends BaseRepository {
    private static instance: UserRepository;

    private constructor() {
        super();
    }

    public static getInstance(): UserRepository {
        if (!UserRepository.instance) {
            UserRepository.instance = new UserRepository();
        }

        return UserRepository.instance;
    }

    public async create(user: UserEntity): Promise<void> {
        this.connection.execute("INSERT INTO USERS(EMAIL, FIRSTNAME, LASTNAME, ADMIN, POINTS, COMPANY_ID, QR, PASSWORD) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", [user.EMAIL, user.FIRSTNAME, user.LASTNAME, user.ADMIN, user.POINTS, user.COMPANY_ID, user.QR, user.PASSWORD]);
    }

    public async read(id: number): Promise<UserEntity> {
       let [result]: any = await this.connection.execute("SELECT * FROM USERS WHERE ID = ?", [id]);
       const user = new UserEntity();
       user.ID = result[0].ID;
       user.EMAIL = result[0].EMAIL;
       user.FIRSTNAME = result[0].FIRSTNAME;
       user.LASTNAME = result[0].LASTNAME;
       user.ADMIN = (result[0].ADMIN === 1);
       user.POINTS = result[0].POINTS;
       user.COMPANY_ID = result[0].COMPANY_ID;
       user.QR = result[0].QR;
       user.PASSWORD = result[0].PASSWORD;

       return user;
    }

    public async update(user: UserEntity): Promise<void> {
    this.connection.execute("UPDATE USERS SET EMAIL = ?, FIRSTNAME = ?, LASTNAME = ?, ADMIN = ?, POINTS = ?, COMPANY_ID = ?, QR = ?, PASSWORD = ? WHERE ID = ?", [user.EMAIL, user.FIRSTNAME, user.LASTNAME, user.ADMIN, user.POINTS, user.COMPANY_ID, user.QR, user.PASSWORD, user.ID]);
    }

    public async delete(user: UserEntity): Promise<undefined> {
        this.connection.execute("DELETE FROM USERS WHERE ID = ?", [user.ID]);
        return undefined;
    }

    public async getUserByEmail(email: string): Promise<UserEntity | null> {
        const [result]: any = await this.connection.execute("SELECT * FROM USERS WHERE EMAIL = ?", [email]);

        if (result.length == 0) return null;

        const user = new UserEntity();
        user.ID = result[0].ID;
        user.EMAIL = result[0].EMAIL;
        user.FIRSTNAME = result[0].FIRSTNAME;
        user.LASTNAME = result[0].LASTNAME;
        user.ADMIN = (result[0].ADMIN === 1);
        user.POINTS = result[0].POINTS;
        user.COMPANY_ID = result[0].COMPANY_ID;
        user.QR = result[0].QR;
        user.PASSWORD = result[0].PASSWORD;

        return user;
    }
}

export default UserRepository;